<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfInstance;
use Carbon\Carbon;

class OfficialLetterService
{
    /**
     * Konversi angka bulan ke angka Romawi.
     */
    public function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $romans[$month] ?? 'I';
    }

    /**
     * Generate nomor surat resmi format kedinasan desa:
     * Contoh: 470/0042/DMS-SKD/XI/2026
     */
    public function generateLetterNumber(ServiceRequest $serviceRequest): string
    {
        $code = strtoupper((string) ($serviceRequest->serviceType?->code ?? 'KET'));
        $paddedId = str_pad((string) $serviceRequest->id, 4, '0', STR_PAD_LEFT);
        $monthRoman = $this->getRomanMonth((int) date('n'));
        $year = date('Y');

        return "470/{$paddedId}/DMS-{$code}/{$monthRoman}/{$year}";
    }

    /**
     * Format nomor HP Indonesia ke format internasional WhatsApp (628...).
     */
    public function formatWhatsAppPhone(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        // Hapus karakter non-digit
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62'.$clean;
        }

        return $clean;
    }

    /**
     * Buat URL dan pesan notifikasi WhatsApp resmi dari Pemerintah Desa.
     *
     * @return array<string, string>
     */
    public function buildWhatsAppNotification(ServiceRequest $serviceRequest): array
    {
        $fullName = (string) $serviceRequest->full_name;
        $ticket = (string) $serviceRequest->ticket_number;
        $serviceName = (string) ($serviceRequest->serviceType?->name ?? 'Layanan Surat');
        $letterNumber = (string) ($serviceRequest->letter_number ?? '-');
        $phone = $this->formatWhatsAppPhone($serviceRequest->phone_number);

        $trackingUrl = url("/lacak?ticket={$ticket}");
        $pdfUrl = url("/api/v1/public/track/{$ticket}/pdf");

        $notesPart = '';
        if (! empty($serviceRequest->admin_notes)) {
            $notesPart = "• Catatan Petugas: {$serviceRequest->admin_notes}\n";
        }

        $message = "Yth. Bapak/Ibu *{$fullName}*,\n\n"
            ."Pemberitahuan dari *Pemerintah Desa Mandiri Sejahtera*:\n"
            ."Permohonan surat Anda dengan nomor tiket *{$ticket}* telah *SELESAI* diproses dan disetujui.\n\n"
            ."*Rincian Surat:*\n"
            ."• Jenis Surat: {$serviceName}\n"
            ."• Nomor Surat Resmi: {$letterNumber}\n"
            .$notesPart."\n"
            ."📄 *Unduh Berkas Surat Resmi (Format PDF):*\n"
            ."{$pdfUrl}\n\n"
            ."Portal Lacak & Status Layanan:\n"
            ."{$trackingUrl}\n\n"
            ."Atau Anda dapat mengambil fisik surat bertanda tangan di Kantor Balai Desa Mandiri Sejahtera pada jam kerja (Senin - Jumat, 08.00 - 15.30 WIB).\n\n"
            .'_Pesan otomatis resmi Sistem Pelayanan Online Desa Mandiri Sejahtera._';

        $encodedText = urlencode($message);
        $whatsappUrl = $phone ? "https://api.whatsapp.com/send?phone={$phone}&text={$encodedText}" : '';

        return [
            'raw_phone' => (string) $serviceRequest->phone_number,
            'formatted_phone' => $phone,
            'message' => $message,
            'whatsapp_url' => $whatsappUrl,
            'pdf_url' => $pdfUrl,
        ];
    }

    /**
     * Hasilkan objek dokumen PDF resmi A4 siap unduh/stream.
     */
    public function generatePdf(ServiceRequest $serviceRequest): DomPdfInstance
    {
        $letter = $this->buildLetterDocument($serviceRequest);
        $pdf = Pdf::loadView('pdf.official_letter', compact('letter'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Bangun struktur data surat resmi yang siap dicetak/diunduh.
     *
     * @return array<string, mixed>
     */
    public function buildLetterDocument(ServiceRequest $serviceRequest): array
    {
        $serviceCode = strtoupper((string) ($serviceRequest->serviceType?->code ?? 'KET'));
        $serviceName = strtoupper((string) ($serviceRequest->serviceType?->name ?? 'SURAT KETERANGAN'));

        $tanggalSurat = $this->formatTanggalIndonesia($serviceRequest->letter_generated_at ?? now());

        $contentParagraph = match ($serviceCode) {
            'SKD' => 'Menerangkan dengan sebenarnya bahwa orang yang bersangkutan di atas adalah benar-benar penduduk dan berdomisili sah di wilayah Desa Mandiri Sejahtera, Kecamatan Mandiri Jaya.',
            'SKTM' => 'Menerangkan dengan sebenarnya bahwa orang yang bersangkutan di atas adalah benar-benar warga penduduk Desa Mandiri Sejahtera yang tergolong dalam keluarga Kurang Mampu / Pra-Sejahtera, dan surat ini diterbitkan untuk keperluan pengajuan keringanan biaya/beasiswa/bantuan sosial.',
            'SKU' => 'Menerangkan dengan sebenarnya bahwa orang yang bersangkutan di atas adalah benar warga Desa Mandiri Sejahtera yang memiliki dan menjalankan kegiatan usaha mikro/kecil aktif di wilayah desa kami.',
            'SKCK' => 'Menerangkan dengan sebenarnya bahwa orang yang bersangkutan di atas adalah benar warga penduduk Desa Mandiri Sejahtera yang berkelakuan baik, tidak sedang menjalani proses hukum pidana, serta tidak pernah terlibat organisasi terlarang.',
            'KTP_KK' => 'Menerangkan bahwa surat ini diterbitkan sebagai surat pengantar resmi permohonan pengurusan dan pencetakan Kartu Tanda Penduduk (KTP) / Kartu Keluarga (KK) ke Dinas Kependudukan dan Pencatatan Sipil.',
            default => 'Menerangkan dengan sebenarnya bahwa orang yang bersangkutan di atas adalah benar warga penduduk yang terdaftar dan memenuhi seluruh persyaratan administrasi di Desa Mandiri Sejahtera.',
        };

        return [
            'letter_number' => $serviceRequest->letter_number ?? $this->generateLetterNumber($serviceRequest),
            'letter_title' => $serviceName,
            'service_code' => $serviceCode,
            'ticket_number' => $serviceRequest->ticket_number,
            'kop' => [
                'pemerintah_kabupaten' => 'PEMERINTAH KABUPATEN BOGOR',
                'kecamatan' => 'KECAMATAN MANDIRI JAYA',
                'desa' => 'PEMERINTAH DESA MANDIRI SEJAHTERA',
                'alamat' => 'Jl. Protokol Desa Mandiri No. 01, Mandiri Jaya, Jawa Barat 16720',
                'kontak' => 'Email: pemdes@mandirisejahtera.desa.id • Telp: (0251) 8123456 • WhatsApp: 0812-3456-7890',
            ],
            'pejabat' => [
                'nama' => 'H. AHMAD SYARIFUDDIN, S.IP',
                'jabatan' => 'Kepala Desa Mandiri Sejahtera',
                'nip' => '19780512 200604 1 008',
            ],
            'pemohon' => [
                'nama' => $serviceRequest->full_name,
                'nik' => $serviceRequest->nik,
                'kk_number' => $serviceRequest->kk_number,
                'alamat' => $serviceRequest->address,
                'keperluan' => $serviceRequest->purpose_description ?: 'Untuk melengkapi berkas persyaratan administrasi yang bersangkutan.',
            ],
            'isi_keterangan' => $contentParagraph,
            'tanggal_terbit' => $tanggalSurat,
            'qr_verification_url' => url("/lacak?ticket={$serviceRequest->ticket_number}"),
            'pdf_download_url' => url("/api/v1/public/track/{$serviceRequest->ticket_number}/pdf"),
            'status' => $serviceRequest->status,
            'tte' => [
                'is_digital' => true,
                'signer_name' => 'H. AHMAD SYARIFUDDIN, S.IP',
                'signer_role' => 'Kepala Desa Mandiri Sejahtera',
                'signer_nip' => '19780512 200604 1 008',
                'certified_by' => 'Balai Sertifikasi Elektronik (BSrE) - BSSN',
                'signed_at' => $tanggalSurat,
                'document_hash' => strtoupper(substr(hash('sha256', ($serviceRequest->ticket_number ?? 'TKT').($serviceRequest->letter_number ?? 'LTR').($serviceRequest->created_at ?? now())), 0, 16)),
                'verification_url' => url("/lacak?ticket={$serviceRequest->ticket_number}"),
            ],
        ];
    }

    /**
     * Format tanggal Indonesia (contoh: 24 November 2026).
     */
    protected function formatTanggalIndonesia($date): string
    {
        $timestamp = $date instanceof Carbon ? $date->timestamp : strtotime((string) $date);
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $hari = date('j', $timestamp);
        $bulan = $bulanIndo[(int) date('n', $timestamp)] ?? date('F', $timestamp);
        $tahun = date('Y', $timestamp);

        return "{$hari} {$bulan} {$tahun}";
    }
}
