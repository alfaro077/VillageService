<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $letter['letter_title'] }} - {{ $letter['letter_number'] }}</title>
  <style>
    @page {
      margin: 12mm 15mm 12mm 15mm;
      size: a4 portrait;
    }
    body {
      font-family: 'Helvetica', 'Arial', sans-serif;
      font-size: 11px;
      line-height: 1.4;
      color: #000000;
      margin: 0;
      padding: 0;
    }
    table {
      border-collapse: collapse;
    }
    .header-table {
      width: 100%;
      margin-bottom: 8px;
    }
    .header-title h3 {
      margin: 0 0 2px 0;
      font-size: 11px;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: #000000;
    }
    .header-title h1 {
      margin: 2px 0 2px 0;
      font-size: 16px;
      font-weight: 900;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: #0b2545;
    }
    .header-title h4 {
      margin: 2px 0 0 0;
      font-size: 11px;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: #000000;
    }
    .header-contact {
      text-align: right;
      font-size: 9.5px;
      line-height: 1.3;
      color: #333333;
    }
    .divider {
      border-bottom: 1px solid #cccccc;
      margin-top: 8px;
      margin-bottom: 14px;
    }
    .meta-table {
      width: 100%;
      margin-bottom: 12px;
      font-size: 11px;
    }
    .meta-table td {
      padding: 1.5px 0;
      vertical-align: top;
    }
    .recipient {
      margin-bottom: 12px;
      font-size: 11px;
      line-height: 1.4;
    }
    .content-paragraph {
      margin-bottom: 10px;
      text-align: justify;
      line-height: 1.45;
    }
    .citizen-table {
      width: 100%;
      margin: 8px 0 10px 18px;
      font-size: 11px;
    }
    .citizen-table td {
      padding: 2.5px 0;
      vertical-align: top;
    }
    .signature-container {
      margin-top: 14px;
      font-size: 11px;
      line-height: 1.3;
    }
    .qr-box {
      margin: 5px 0;
    }
    .official-name {
      font-size: 11px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: 4px;
    }
    .official-nip {
      font-family: monospace;
      font-size: 10px;
      color: #333333;
    }
  </style>
</head>
<body>

  <!-- KOP SURAT RESMI -->
  <table class="header-table">
    <tr>
      <!-- Logo Kotak Biru di Kiri -->
      <td style="width: 80px; vertical-align: middle;">
        <div style="width: 72px; height: 86px; background-color: #0b2545; text-align: center; padding-top: 8px;">
          <svg viewBox="0 0 100 100" width="60" height="60" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="46" fill="#0f2b5c" stroke="#facc15" stroke-width="2.5"/>
            <circle cx="50" cy="50" r="41" fill="none" stroke="#facc15" stroke-width="1" stroke-dasharray="2,2"/>
            <circle cx="50" cy="50" r="37" fill="#133e7c" stroke="#facc15" stroke-width="0.8"/>
            <polygon points="50,16 52,22 58,22 53,26 55,32 50,28 45,32 47,26 42,22 48,22" fill="#facc15"/>
            <polygon points="50,84 52,78 58,78 53,74 55,68 50,72 45,68 47,74 42,78 48,78" fill="#facc15"/>
            <path d="M50 24 L55 45 L76 50 L55 55 L50 76 L45 55 L24 50 L45 45 Z" fill="#ffffff" stroke="#93c5fd" stroke-width="1"/>
            <path d="M32 32 L50 45 L68 32 L55 50 L68 68 L50 55 L32 68 L45 50 Z" fill="#facc15" stroke="#ca8a04" stroke-width="0.8"/>
            <circle cx="50" cy="50" r="10" fill="#0b2545" stroke="#facc15" stroke-width="2"/>
            <circle cx="50" cy="50" r="4" fill="#ffffff"/>
          </svg>
        </div>
      </td>

      <!-- Identitas Instansi di Tengah -->
      <td class="header-title" style="padding-left: 12px; vertical-align: middle;">
        <h3>{{ $letter['kop']['pemerintah_kabupaten'] ?? 'PEMERINTAH KABUPATEN BOGOR' }}</h3>
        <h3>{{ $letter['kop']['kecamatan'] ?? 'KECAMATAN MANDIRI JAYA' }}</h3>
        <h1>{{ $letter['kop']['desa'] ?? 'PEMERINTAH DESA MANDIRI SEJAHTERA' }}</h1>
        <h4>PROVINSI JAWA BARAT</h4>
      </td>

      <!-- Kontak di Kanan -->
      <td class="header-contact" style="width: 140px; vertical-align: middle;">
        <strong>Kantor Balai Desa Mandiri</strong><br>
        Karanganyar Mandiri<br>
        Bogor 16720<br>
        <span style="font-family: monospace;">0812-3456-7890</span><br>
        pemdes@desa.id
      </td>
    </tr>
  </table>

  <!-- Garis Pembatas Halus Bawah Kop -->
  <div class="divider"></div>

  <!-- Nomor, Lampiran, & Perihal Surat -->
  <table class="meta-table">
    <tr>
      <td style="width: 75px;">Nomor</td>
      <td style="width: 12px;">:</td>
      <td style="font-family: monospace; font-weight: bold;">{{ $letter['letter_number'] }}</td>
    </tr>
    <tr>
      <td>Lamp.</td>
      <td>:</td>
      <td>-</td>
    </tr>
    <tr>
      <td>Hal.</td>
      <td>:</td>
      <td><strong>{{ $letter['letter_title'] }}</strong></td>
    </tr>
  </table>

  <!-- Penerima / Tujuan Surat -->
  <div class="recipient">
    Kepada Yth Bapak/Ibu<br>
    <strong>{{ $letter['pemohon']['nama'] ?? 'Warga Pemohon' }} / Pihak Terkait</strong><br>
    Di-<br>
    &nbsp;&nbsp;&nbsp;&nbsp;Tempat
  </div>

  <!-- Isi Tubuh Surat -->
  <div class="content-paragraph">
    <strong>Assalamualaikum Wr. Wb.</strong>
  </div>

  <div class="content-paragraph">
    Kepala Desa Mandiri Sejahtera, Kecamatan Mandiri Jaya, Kabupaten Bogor, Provinsi Jawa Barat dengan ini menerangkan bahwa:
  </div>

  <!-- Daftar Data Resmi Warga Pemohon (Format Teks Sejajar Tanpa Tabel) -->
  <table class="citizen-table">
    <tr>
      <td style="width: 210px; color: #222222;">Nama Lengkap</td>
      <td style="width: 15px; text-align: center; font-weight: bold;">:</td>
      <td style="font-weight: bold; text-transform: uppercase;">{{ $letter['pemohon']['nama'] }}</td>
    </tr>
    <tr>
      <td style="color: #222222;">Nomor Induk Kependudukan (NIK)</td>
      <td style="text-align: center; font-weight: bold;">:</td>
      <td style="font-family: monospace; font-weight: bold; letter-spacing: 1px;">{{ $letter['pemohon']['nik'] }}</td>
    </tr>
    <tr>
      <td style="color: #222222;">Nomor Kartu Keluarga (No KK)</td>
      <td style="text-align: center; font-weight: bold;">:</td>
      <td style="font-family: monospace; font-weight: bold; letter-spacing: 1px;">{{ $letter['pemohon']['kk_number'] }}</td>
    </tr>
    <tr>
      <td style="color: #222222; vertical-align: top;">Alamat Domisili</td>
      <td style="text-align: center; vertical-align: top; font-weight: bold;">:</td>
      <td style="line-height: 1.4;">{{ $letter['pemohon']['alamat'] }}</td>
    </tr>
    <tr>
      <td style="color: #222222;">Keperluan Surat</td>
      <td style="text-align: center; font-weight: bold;">:</td>
      <td style="font-weight: bold;">{{ $letter['pemohon']['keperluan'] }}</td>
    </tr>
  </table>

  <div class="content-paragraph">
    {{ $letter['isi_keterangan'] }} Dokumen resmi ini diterbitkan secara sah berdasarkan data kependudukan terverifikasi.
  </div>

  <div class="content-paragraph">
    Demikian permohonan ini kami sampaikan. Atas perhatian dan perkenan Bapak/Ibu, kami ucapkan terima kasih.
  </div>

  <div class="content-paragraph">
    <strong>Wassalamualaikum Wr. Wb.</strong>
  </div>

  <!-- Bagian Tanda Tangan Digital Resmi (Kiri Bawah) -->
  <div class="signature-container">
    Mandiri Jaya, {{ $letter['tanggal_terbit'] }}<br>
    {{ $letter['pejabat']['jabatan'] ?? 'Kepala Desa' }},<br>

    <!-- QR Code TTE Resmi -->
    <div class="qr-box">
      <svg viewBox="0 0 100 100" width="80" height="80" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0h30v30H0zm4 4v22h22V4zm4 4h14v14H8zM70 0h30v30H70zm4 4v22h22V4zm4 4h14v14H78zM0 70h30v30H0zm4 4v22h22V74zm4 4h14v14H8z"/>
        <path d="M34 2h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm-24 8h4v4h-4zm16 0h4v4h-4zm-16 8h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm-20 8h4v4h-4zm12 0h4v4h-4zm-16 8h4v4h-4zm12 0h4v4h-4zm16 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm-44 8h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm24 0h4v4h-4zm-48 8h4v4h-4zm8 0h4v4h-4zm12 0h4v4h-4zm24 0h4v4h-4zm8 0h4v4h-4zm-52 8h4v4h-4zm24 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm-48 8h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm16 0h4v4h-4zm-40 8h4v4h-4zm16 0h4v4h-4zm8 0h4v4h-4zm16 0h4v4h-4zm8 0h4v4h-4zm-36 8h4v4h-4zm8 0h4v4h-4zm12 0h4v4h-4zm8 0h4v4h-4zm8 0h4v4h-4z"/>
        <circle cx="50" cy="50" r="14" fill="#ffffff" stroke="#000000" stroke-width="0.8"/>
        <circle cx="50" cy="50" r="11" fill="#0b2545"/>
        <circle cx="50" cy="50" r="9.5" fill="none" stroke="#facc15" stroke-width="0.8"/>
        <path d="M50 42 L52 47 L58 48 L53 52 L55 58 L50 54 L45 58 L47 52 L42 48 L48 47 Z" fill="#facc15"/>
      </svg>
    </div>

    <div class="official-name">{{ $letter['pejabat']['nama'] ?? 'H. AHMAD SYARIFUDDIN, S.IP' }}</div>
    <div class="official-nip">NIP. {{ $letter['pejabat']['nip'] ?? '19780512 200604 1 008' }}</div>
  </div>

</body>
</html>

