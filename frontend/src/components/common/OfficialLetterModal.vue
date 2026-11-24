<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 print:p-0 print:bg-white print:fixed-none">
    <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col my-4 print:border-none print:shadow-none print:m-0 print:max-w-none print:w-full">
      
      <!-- Top Action Bar (Hidden when printing) -->
      <div class="px-6 py-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3 print:hidden">
        <div class="flex items-center gap-2.5">
          <span class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
            📄
          </span>
          <div>
            <h3 class="text-sm font-bold leading-tight">Dokumen Surat Resmi Diterbitkan</h3>
            <p class="text-[11px] text-slate-300 font-mono">{{ activeLetter?.letter_number || 'Dokumen Resmi Desa' }}</p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- Tombol Kirim File PDF ke WhatsApp -->
          <button
            v-if="effectiveWhatsappUrl"
            @click="handleSendPdfToWhatsApp"
            :disabled="isSendingWhatsapp"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm cursor-pointer disabled:opacity-50"
            title="Otomatis unduh berkas PDF dan buka WhatsApp pemohon"
          >
            <svg v-if="isSendingWhatsapp" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span>Kirim File PDF ke WhatsApp</span>
          </button>

          <!-- Tombol Unduh File PDF Asli Langsung -->
          <button
            @click="handleDownloadPdf"
            :disabled="isDownloadingPdf"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-sm cursor-pointer disabled:opacity-50"
            title="Unduh langsung berkas .pdf resmi desa"
          >
            <svg v-if="isDownloadingPdf" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Unduh File PDF</span>
          </button>

          <!-- Tombol Cetak Dialog Browser -->
          <button
            @click="handlePrint"
            class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all shadow-sm cursor-pointer border border-slate-700"
            title="Cetak lewat dialog peramban"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak</span>
          </button>

          <!-- Close Button -->
          <button
            @click="$emit('close')"
            class="p-2 text-slate-400 hover:text-white rounded-lg transition-colors cursor-pointer"
            title="Tutup"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Banner Panduan Pengiriman Dokumen PDF ke WhatsApp -->
      <div v-if="noticeMessage" class="px-6 py-3 bg-emerald-50 border-b border-emerald-200 text-emerald-900 text-xs flex items-center justify-between gap-3 animate-in fade-in duration-200 print:hidden">
        <div class="flex items-center gap-2">
          <span class="text-base shrink-0">📎</span>
          <p class="font-medium leading-relaxed">{{ noticeMessage }}</p>
        </div>
        <button @click="noticeMessage = ''" class="text-emerald-700 hover:text-emerald-950 font-bold text-xs shrink-0 cursor-pointer">✕</button>
      </div>

      <!-- Scrollable Paper Container -->
      <div class="overflow-y-auto max-h-[85vh] p-4 sm:p-8 bg-slate-200/80 flex justify-center print:p-0 print:m-0 print:bg-white print:max-h-none print:overflow-visible">
        
        <!-- Official Indonesian Letter Paper (Format 1 Halaman Bersih & Pas) -->
        <div id="official-letter-paper" class="bg-white text-black w-full max-w-3xl p-6 sm:p-10 shadow-md font-sans text-xs leading-normal print:shadow-none print:max-w-none print:w-full print:border-none print:p-0">
          
          <!-- KOP SURAT RESMI (Logo Biru di Kiri, Nama Instansi di Tengah, Kontak di Kanan) -->
          <div class="flex items-center justify-between gap-3 sm:gap-4 pb-3 border-b border-black/10">
            
            <!-- 1. Logo Kotak Biru dengan Emblem Emas di Sebelah Kiri -->
            <div class="w-18 sm:w-22 h-24 sm:h-28 bg-[#0b2545] flex items-center justify-center p-1.5 shrink-0 shadow-2xs">
              <svg viewBox="0 0 100 100" class="w-16 h-16 sm:w-20 sm:h-20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Lingkaran Luar Emas -->
                <circle cx="50" cy="50" r="46" fill="#0f2b5c" stroke="#facc15" stroke-width="2.5"/>
                <circle cx="50" cy="50" r="41" fill="none" stroke="#facc15" stroke-width="1" stroke-dasharray="2,2"/>
                <circle cx="50" cy="50" r="37" fill="#133e7c" stroke="#facc15" stroke-width="0.8"/>
                <!-- Ornamen Bintang Lingkar -->
                <polygon points="50,16 52,22 58,22 53,26 55,32 50,28 45,32 47,26 42,22 48,22" fill="#facc15"/>
                <polygon points="50,84 52,78 58,78 53,74 55,68 50,72 45,68 47,74 42,78 48,78" fill="#facc15"/>
                <!-- Lambang Bintang Segi Delapan Pusat -->
                <path d="M50 24 L55 45 L76 50 L55 55 L50 76 L45 55 L24 50 L45 45 Z" fill="#ffffff" stroke="#93c5fd" stroke-width="1"/>
                <path d="M32 32 L50 45 L68 32 L55 50 L68 68 L50 55 L32 68 L45 50 Z" fill="#facc15" stroke="#ca8a04" stroke-width="0.8"/>
                <!-- Titik Pusat Biru dan Emas -->
                <circle cx="50" cy="50" r="10" fill="#0b2545" stroke="#facc15" stroke-width="2"/>
                <circle cx="50" cy="50" r="4" fill="#ffffff"/>
              </svg>
            </div>

            <!-- 2. Teks Nama Instansi (Di samping logo) -->
            <div class="flex-1 px-2 sm:px-3 leading-tight">
              <h3 class="text-xs sm:text-sm font-semibold tracking-wide text-black uppercase">
                {{ activeLetter?.kop?.pemerintah_kabupaten || 'PEMERINTAH KABUPATEN BOGOR' }}
              </h3>
              <h3 class="text-xs sm:text-sm font-bold tracking-wide text-black uppercase mt-0.5">
                {{ activeLetter?.kop?.kecamatan || 'KECAMATAN MANDIRI JAYA' }}
              </h3>
              <h1 class="text-base sm:text-xl font-black tracking-wide text-[#0b2545] uppercase mt-0.5 leading-tight">
                {{ activeLetter?.kop?.desa || 'PEMERINTAH DESA MANDIRI SEJAHTERA' }}
              </h1>
              <h4 class="text-xs sm:text-sm font-bold tracking-wider text-black uppercase mt-0.5">
                PROVINSI JAWA BARAT
              </h4>
            </div>

            <!-- 3. Alamat & Kontak di Sebelah Kanan (Right-aligned persis referensi) -->
            <div class="text-right text-[10px] sm:text-[11px] leading-snug text-slate-700 shrink-0 font-sans hidden sm:block">
              <p class="font-medium text-black">Kantor Balai Desa Mandiri</p>
              <p>Karanganyar Mandiri</p>
              <p>Bogor 16720</p>
              <p class="font-mono text-[10px]">0812-3456-7890</p>
              <p class="text-slate-800">pemdes@desa.id</p>
            </div>
          </div>

          <!-- Bagian Nomor, Lampiran, dan Perihal Surat -->
          <div class="mt-4 mb-3 space-y-0.5 text-xs">
            <div class="grid grid-cols-12 gap-1">
              <span class="col-span-3 sm:col-span-2">Nomor :</span>
              <span class="col-span-9 sm:col-span-10 font-mono text-black font-semibold">{{ activeLetter?.letter_number || '470/0001/DMS-SKD/XI/2026' }}</span>
            </div>
            <div class="grid grid-cols-12 gap-1">
              <span class="col-span-3 sm:col-span-2">Lamp. :</span>
              <span class="col-span-9 sm:col-span-10">-</span>
            </div>
            <div class="grid grid-cols-12 gap-1">
              <span class="col-span-3 sm:col-span-2">Hal.   :</span>
              <span class="col-span-9 sm:col-span-10 font-bold text-black">{{ activeLetter?.letter_title || 'Surat Keterangan Pelayanan Desa' }}</span>
            </div>
          </div>

          <!-- Penerima / Tujuan Surat -->
          <div class="my-3 text-xs space-y-0.5">
            <p>Kepada Yth Bapak/Ibu</p>
            <p class="font-bold text-black">{{ residentName }} / Pihak Terkait</p>
            <p>Di-</p>
            <p class="pl-6">Tempat</p>
          </div>

          <!-- Isi Tubuh Surat -->
          <div class="space-y-2.5 text-xs text-justify leading-relaxed">
            <p class="font-bold">Assalamualaikum Wr. Wb.</p>

            <p>
              Kepala Desa Mandiri Sejahtera, Kecamatan Mandiri Jaya, Kabupaten Bogor, Provinsi Jawa Barat dengan ini menerangkan bahwa:
            </p>

            <!-- DAFTAR DATA RESMI WARGA PEMOHON (FORMAT TEKS SEJAJAR RAPI TANPA TABEL) -->
            <div class="my-2.5 pl-4 sm:pl-6 space-y-1.5 text-xs">
              <div class="grid grid-cols-12 gap-1 items-start">
                <span class="col-span-4 sm:col-span-3 text-slate-800 font-medium">Nama Lengkap</span>
                <span class="col-span-1 text-center font-bold">:</span>
                <span class="col-span-7 sm:col-span-8 font-bold text-black uppercase">{{ residentName }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1 items-start">
                <span class="col-span-4 sm:col-span-3 text-slate-800 font-medium">Nomor Induk Kependudukan (NIK)</span>
                <span class="col-span-1 text-center font-bold">:</span>
                <span class="col-span-7 sm:col-span-8 font-mono font-bold text-black tracking-wider">{{ residentNik }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1 items-start">
                <span class="col-span-4 sm:col-span-3 text-slate-800 font-medium">Nomor Kartu Keluarga (No KK)</span>
                <span class="col-span-1 text-center font-bold">:</span>
                <span class="col-span-7 sm:col-span-8 font-mono font-bold text-black tracking-wider">{{ residentKk }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1 items-start">
                <span class="col-span-4 sm:col-span-3 text-slate-800 font-medium">Alamat Domisili</span>
                <span class="col-span-1 text-center font-bold">:</span>
                <span class="col-span-7 sm:col-span-8 text-black leading-relaxed">{{ residentAddress }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1 items-start">
                <span class="col-span-4 sm:col-span-3 text-slate-800 font-medium">Keperluan Surat</span>
                <span class="col-span-1 text-center font-bold">:</span>
                <span class="col-span-7 sm:col-span-8 font-semibold text-black">{{ residentPurpose }}</span>
              </div>
            </div>

            <p>
              {{ activeLetter?.isi_keterangan || 'Menerangkan dengan sebenarnya bahwa orang yang bersangkutan di atas adalah benar-benar warga penduduk Desa Mandiri Sejahtera dan memenuhi persyaratan administrasi yang sah.' }} Dokumen resmi ini diterbitkan secara sah berdasarkan data kependudukan terverifikasi.
            </p>

            <p>
              Demikian permohonan ini kami sampaikan. Atas perhatian dan perkenan Bapak/Ibu, kami ucapkan terima kasih.
            </p>

            <p class="font-bold">Wassalamualaikum Wr. Wb.</p>
          </div>

          <!-- Tanda Tangan di Sebelah Kiri Bawah (Persis seperti gambar referensi) -->
          <div class="mt-4 text-xs text-left space-y-0.5">
            <p>Mandiri Jaya, {{ activeLetter?.tanggal_terbit || '24 November 2026' }}</p>
            <p>{{ activeLetter?.pejabat?.jabatan || 'Kepala Desa' }},</p>

            <!-- QR Code Besar TTE Resmi dengan Lambang di Tengah -->
            <div class="my-1.5 inline-block">
              <div class="w-22 h-22 sm:w-24 sm:h-24 bg-white p-1 border border-black/30 shadow-2xs relative flex items-center justify-center">
                <!-- SVG QR Code Matrix dengan logo tengah -->
                <svg viewBox="0 0 100 100" class="w-full h-full text-black fill-current" xmlns="http://www.w3.org/2000/svg">
                  <!-- Finder patterns -->
                  <path d="M0 0h30v30H0zm4 4v22h22V4zm4 4h14v14H8zM70 0h30v30H70zm4 4v22h22V4zm4 4h14v14H78zM0 70h30v30H0zm4 4v22h22V74zm4 4h14v14H8z"/>
                  <!-- Matrix modules -->
                  <path d="M34 2h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm-24 8h4v4h-4zm16 0h4v4h-4zm-16 8h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm-20 8h4v4h-4zm12 0h4v4h-4zm-16 8h4v4h-4zm12 0h4v4h-4zm16 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm-44 8h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm24 0h4v4h-4zm-48 8h4v4h-4zm8 0h4v4h-4zm12 0h4v4h-4zm24 0h4v4h-4zm8 0h4v4h-4zm-52 8h4v4h-4zm24 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm-48 8h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm16 0h4v4h-4zm-40 8h4v4h-4zm16 0h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm8 0h4v4h-4zm-36 8h4v4h-4zm8 0h4v4h-4zm12 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4z"/>
                  <!-- Lingkaran masker putih tengah -->
                  <circle cx="50" cy="50" r="14" fill="#ffffff" stroke="#000000" stroke-width="0.8"/>
                  <!-- Badge biru dan bintang emas tengah -->
                  <circle cx="50" cy="50" r="11" fill="#0b2545"/>
                  <circle cx="50" cy="50" r="9.5" fill="none" stroke="#facc15" stroke-width="0.8"/>
                  <path d="M50 42 L52 47 L58 48 L53 52 L55 58 L50 54 L45 58 L47 52 L42 48 L48 47 Z" fill="#facc15"/>
                </svg>
              </div>
            </div>

            <!-- Nama Pejabat & NIP -->
            <p class="font-bold text-black uppercase tracking-wide text-xs">
              {{ activeLetter?.pejabat?.nama || 'H. AHMAD SYARIFUDDIN, S.IP' }}
            </p>
            <p class="text-slate-700 font-mono text-[11px]">
              NIP. {{ activeLetter?.pejabat?.nip || '19780512 200604 1 008' }}
            </p>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { adminService } from '../../services/adminService';
import { publicService } from '../../services/publicService';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  letterData: {
    type: Object,
    default: null,
  },
  whatsappUrl: {
    type: String,
    default: '',
  },
  serviceId: {
    type: [Number, String],
    default: null,
  },
  ticketNumber: {
    type: String,
    default: '',
  },
});

defineEmits(['close']);

const isDownloadingPdf = ref(false);
const isSendingWhatsapp = ref(false);
const noticeMessage = ref('');

// Dapatkan objek data surat resmi (menangani jika response dari backend dibungkus { document: ..., whatsapp: ... } ataupun objek langsung)
const activeLetter = computed(() => {
  if (!props.letterData) return null;
  return props.letterData.document || props.letterData;
});

// Getter data warga dengan fallback yang aman dan fleksibel
const residentName = computed(() => {
  const d = activeLetter.value;
  return d?.pemohon?.nama || d?.pemohon?.full_name || d?.full_name || d?.nama || '-';
});

const residentNik = computed(() => {
  const d = activeLetter.value;
  return d?.pemohon?.nik || d?.nik || '-';
});

const residentKk = computed(() => {
  const d = activeLetter.value;
  return d?.pemohon?.kk_number || d?.kk_number || '-';
});

const residentAddress = computed(() => {
  const d = activeLetter.value;
  return d?.pemohon?.alamat || d?.pemohon?.address || d?.address || d?.alamat || '-';
});

const residentPurpose = computed(() => {
  const d = activeLetter.value;
  return d?.pemohon?.keperluan || d?.pemohon?.purpose_description || d?.purpose_description || d?.keperluan || '-';
});

const effectiveWhatsappUrl = computed(() => {
  return props.whatsappUrl || activeLetter.value?.whatsapp?.whatsapp_url || props.letterData?.whatsapp?.whatsapp_url || '';
});

const getFilename = () => {
  const name = residentName.value !== '-' ? residentName.value.replace(/[^a-zA-Z0-9]/g, '_') : 'Warga';
  const cleanNo = (activeLetter.value?.letter_number || 'DMS').replace(/[^a-zA-Z0-9]/g, '_');
  return `Surat_${cleanNo}_${name}.pdf`;
};

const handleDownloadPdf = async () => {
  isDownloadingPdf.value = true;
  noticeMessage.value = '';
  const filename = getFilename();
  const id = props.serviceId || activeLetter.value?.id;
  const ticket = props.ticketNumber || activeLetter.value?.ticket_number;

  try {
    if (id) {
      await adminService.downloadServicePdf(id, filename);
    } else if (ticket) {
      await publicService.downloadPublicPdf(ticket, filename);
    } else {
      window.print();
    }
    noticeMessage.value = `Berkas PDF "${filename}" berhasil diunduh.`;
    setTimeout(() => (noticeMessage.value = ''), 4000);
  } catch (err) {
    console.error('Gagal unduh PDF API, fallback ke print peramban.', err);
    window.print();
  } finally {
    isDownloadingPdf.value = false;
  }
};

const handleSendPdfToWhatsApp = async () => {
  isSendingWhatsapp.value = true;
  noticeMessage.value = '';
  const filename = getFilename();
  const id = props.serviceId || activeLetter.value?.id;
  const ticket = props.ticketNumber || activeLetter.value?.ticket_number;

  let downloadedBlob = null;
  try {
    if (id) {
      downloadedBlob = await adminService.downloadServicePdf(id, filename);
    } else if (ticket) {
      downloadedBlob = await publicService.downloadPublicPdf(ticket, filename);
    }
  } catch (err) {
    console.warn('Gagal unduh blob PDF.', err);
  }

  // Jika peramban mendukung Web Share API dengan berkas dokumen (contoh: di ponsel/tablet)
  if (downloadedBlob && navigator.canShare) {
    try {
      const file = new File([downloadedBlob], filename, { type: 'application/pdf' });
      if (navigator.canShare({ files: [file] })) {
        await navigator.share({
          files: [file],
          title: `Surat Resmi Desa - ${filename}`,
          text: `Berikut kami lampirkan berkas dokumen surat resmi Anda dari Pemerintah Desa Mandiri Sejahtera.`,
        });
        isSendingWhatsapp.value = false;
        return;
      }
    } catch (shareErr) {
      console.log('Share API dibatalkan/tidak didukung, melanjutkan ke obrolan WhatsApp Web.');
    }
  }

  // Buka tautan obrolan WhatsApp pemohon (yang menyertakan tautan unduh PDF resmi)
  if (effectiveWhatsappUrl.value) {
    window.open(effectiveWhatsappUrl.value, '_blank');
  }

  noticeMessage.value = `Berkas PDF "${filename}" telah otomatis terunduh ke komputer Anda. Silakan lampirkan (drag-and-drop / klik ikon lampiran) berkas PDF tersebut ke ruang obrolan WhatsApp pemohon yang baru saja terbuka.`;
  setTimeout(() => (noticeMessage.value = ''), 12000);

  isSendingWhatsapp.value = false;
};

const handlePrint = () => {
  window.print();
};
</script>

<style scoped>
@media print {
  @page {
    size: A4 portrait;
    margin: 8mm 12mm 8mm 12mm;
  }

  /* Kunci tinggi html dan body agar tidak ada scroll atau overflow ruang kosong */
  html, body {
    height: 100% !important;
    max-height: 100% !important;
    overflow: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
  }

  /* Sembunyikan semua elemen selain kontainer kertas surat */
  body * {
    visibility: hidden;
  }
  
  /* Hanya tampilkan kertas surat */
  #official-letter-paper, #official-letter-paper * {
    visibility: visible;
  }

  #official-letter-paper {
    position: fixed !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
    border: none !important;
    background: transparent !important;
    page-break-before: avoid !important;
    page-break-after: avoid !important;
    page-break-inside: avoid !important;
    break-before: avoid !important;
    break-after: avoid !important;
    break-inside: avoid !important;
  }
}
</style>
