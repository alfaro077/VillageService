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
            <p class="text-[11px] text-slate-300 font-mono">{{ letterData?.letter_number || 'Dokumen Resmi Desa' }}</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <!-- WhatsApp Button (if available) -->
          <a
            v-if="whatsappUrl"
            :href="whatsappUrl"
            target="_blank"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm"
          >
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span>Kirim ke WhatsApp</span>
          </a>

          <!-- Print / Download PDF Button -->
          <button
            @click="handlePrint"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-sm cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak / Simpan PDF</span>
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

      <!-- Scrollable Paper Container -->
      <div class="overflow-y-auto max-h-[80vh] p-4 sm:p-8 bg-slate-100 flex justify-center print:p-0 print:m-0 print:bg-white print:max-h-none print:overflow-visible">
        
        <!-- Official Indonesian Letter Paper (A4 Style) -->
        <div id="official-letter-paper" class="bg-white text-slate-900 w-full max-w-2xl p-8 sm:p-12 shadow-md border border-slate-200 print:shadow-none print:border-none print:p-0 print:max-w-none print:w-full font-serif text-sm leading-relaxed">
          
          <!-- KOP SURAT RESMI DESA (LOGO DI PINGGIR KIRI) -->
          <div class="pb-3 border-b-4 border-double border-slate-900 flex items-center gap-3 sm:gap-5 relative">
            <!-- LOGO INSTANSI DI PINGGIR KIRI -->
            <div class="shrink-0 flex items-center justify-center">
              <div class="w-20 h-22 sm:w-24 sm:h-26 flex flex-col items-center justify-center">
                <!-- Lambang Resmi Daerah / Lambang Perisai Garuda & Pemda (Vector SVG Berkualitas Tinggi) -->
                <svg viewBox="0 0 100 115" class="w-16 h-18 sm:w-20 sm:h-22 drop-shadow-xs" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Perisai Luar Lambang Daerah -->
                  <path d="M50 4 L88 18 C88 68 50 106 50 110 C50 106 12 68 12 18 Z" fill="#0f172a" stroke="#020617" stroke-width="2.5"/>
                  <!-- Perisai Lapis Dalam -->
                  <path d="M50 9 L83 22 C83 65 50 100 50 103 C50 100 17 65 17 22 Z" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5"/>
                  <!-- Latar Belakang Perisai Merah Putih -->
                  <path d="M50 12 L80 24 C80 50 50 50 50 50 L20 50 C20 24 50 12 50 12 Z" fill="#dc2626"/>
                  <path d="M20 50 C20 70 50 96 50 96 C50 96 80 70 80 50 Z" fill="#f8fafc"/>
                  <!-- Bintang Emas Simbol Ketuhanan -->
                  <polygon points="50,18 53,26 62,26 55,31 58,39 50,34 42,39 45,31 38,26 47,26" fill="#facc15" stroke="#ca8a04" stroke-width="0.8"/>
                  <!-- Untaian Padi & Kapas Kesejahteraan -->
                  <path d="M26 38 C23 60 35 78 50 86 C65 78 77 60 74 38" stroke="#16a34a" stroke-width="3" stroke-linecap="round" fill="none"/>
                  <!-- Gapura / Monumen Pusat Kemandirian -->
                  <rect x="45" y="44" width="10" height="24" fill="#1e3a8a" rx="1"/>
                  <polygon points="50,36 41,44 59,44" fill="#fbbf24"/>
                  <!-- Gelombang Air / Sungai Kemakmuran -->
                  <path d="M34 74 Q50 67 66 74" stroke="#0284c7" stroke-width="2.5" fill="none"/>
                  <path d="M36 80 Q50 73 64 80" stroke="#0284c7" stroke-width="2" fill="none"/>
                  <!-- Pita Semboyan Daerah di Bawah -->
                  <path d="M22 93 L78 93 L73 100 L27 100 Z" fill="#1e293b"/>
                  <text x="50" y="98.5" text-anchor="middle" font-size="5" font-family="sans-serif" font-weight="900" fill="#ffffff" letter-spacing="1.5">BOGOR</text>
                </svg>
              </div>
            </div>

            <!-- TEKS KOP RESMI (Center-aligned) -->
            <div class="flex-1 text-center pr-2 sm:pr-6">
              <h4 class="text-xs sm:text-sm font-bold uppercase tracking-widest text-slate-800">
                {{ letterData?.kop?.pemerintah_kabupaten || 'PEMERINTAH KABUPATEN BOGOR' }}
              </h4>
              <h4 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800">
                {{ letterData?.kop?.kecamatan || 'KECAMATAN MANDIRI JAYA' }}
              </h4>
              <h2 class="text-lg sm:text-2xl font-black uppercase tracking-wide text-slate-950 mt-0.5 leading-tight">
                {{ letterData?.kop?.desa || 'PEMERINTAH DESA MANDIRI SEJAHTERA' }}
              </h2>
              <p class="text-[11px] font-sans text-slate-600 mt-1 leading-snug">
                {{ letterData?.kop?.alamat || 'Jl. Protokol Desa Mandiri No. 01, Mandiri Jaya, Jawa Barat 16720' }}
              </p>
              <p class="text-[10px] font-sans text-slate-500 mt-0.5">
                {{ letterData?.kop?.kontak || 'Email: pemdes@mandirisejahtera.desa.id • Telp: (0251) 8123456 • WhatsApp: 0812-3456-7890' }}
              </p>
            </div>
          </div>

          <!-- JUDUL & NOMOR SURAT -->
          <div class="text-center my-6">
            <h3 class="text-base sm:text-lg font-bold uppercase tracking-wider underline decoration-2 underline-offset-4 text-slate-950">
              {{ letterData?.letter_title || 'SURAT KETERANGAN' }}
            </h3>
            <p class="text-xs font-mono font-medium text-slate-700 mt-1">
              Nomor: {{ letterData?.letter_number || '470/..../DMS/..../2026' }}
            </p>
          </div>

          <!-- PEMBUKA SURAT -->
          <div class="space-y-4 text-justify text-xs sm:text-sm">
            <p>
              Yang bertanda tangan di bawah ini, Kepala Desa Mandiri Sejahtera, Kecamatan Mandiri Jaya, Kabupaten Bogor, Provinsi Jawa Barat, dengan ini menerangkan bahwa:
            </p>

            <!-- BIODATA WARGA PEMOHON -->
            <div class="my-4 pl-4 sm:pl-8 space-y-1.5 font-sans text-xs">
              <div class="grid grid-cols-12 gap-1">
                <span class="col-span-4 font-semibold text-slate-600">Nama Lengkap</span>
                <span class="col-span-1">:</span>
                <span class="col-span-7 font-bold text-slate-900 uppercase">{{ letterData?.pemohon?.nama || '-' }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1">
                <span class="col-span-4 font-semibold text-slate-600">Nomor Induk Kependudukan (NIK)</span>
                <span class="col-span-1">:</span>
                <span class="col-span-7 font-mono font-bold text-slate-900">{{ letterData?.pemohon?.nik || '-' }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1">
                <span class="col-span-4 font-semibold text-slate-600">Nomor Kartu Keluarga (KK)</span>
                <span class="col-span-1">:</span>
                <span class="col-span-7 font-mono font-bold text-slate-900">{{ letterData?.pemohon?.kk_number || '-' }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1">
                <span class="col-span-4 font-semibold text-slate-600">Alamat Tempat Tinggal</span>
                <span class="col-span-1">:</span>
                <span class="col-span-7 text-slate-900">{{ letterData?.pemohon?.alamat || '-' }}</span>
              </div>
              <div class="grid grid-cols-12 gap-1">
                <span class="col-span-4 font-semibold text-slate-600">Keperluan Surat</span>
                <span class="col-span-1">:</span>
                <span class="col-span-7 text-slate-900 font-medium">{{ letterData?.pemohon?.keperluan || '-' }}</span>
              </div>
            </div>

            <!-- ISI KETERANGAN RESMI -->
            <p>
              {{ letterData?.isi_keterangan || 'Menerangkan dengan sebenarnya bahwa orang yang bersangkutan di atas adalah benar-benar warga penduduk Desa Mandiri Sejahtera dan memenuhi persyaratan administrasi yang sah.' }}
            </p>

            <!-- PENUTUP SURAT -->
            <p>
              Demikian Surat Keterangan ini kami buat dan berikan kepada yang bersangkutan dengan sebenarnya, agar dapat dipergunakan sebagaimana mestinya dan sesuai dengan ketentuan hukum yang berlaku.
            </p>
          </div>

          <!-- PENGESAHAN TANDA TANGAN DIGITAL (TTE) RESMI -->
          <div class="mt-8 pt-2">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6">
              
              <!-- Kolom Kiri: Verifikasi Keaslian & Segel Digital -->
              <div class="text-left font-sans text-[10px] text-slate-600 max-w-[240px] space-y-1.5 order-2 sm:order-1">
                <div class="flex items-center gap-1.5 text-blue-900 font-bold text-[11px]">
                  <svg class="w-4 h-4 text-blue-700 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                  </svg>
                  <span>Integritas Dokumen Terverifikasi</span>
                </div>
                <p class="text-[9px] text-slate-500 leading-tight">
                  Dokumen ini diterbitkan secara sah oleh Pemerintah Desa Mandiri Sejahtera dan tercatat dalam pangkalan data arsip digital resmi.
                </p>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 font-mono text-[9px] space-y-0.5">
                  <div class="flex justify-between text-slate-500">
                    <span>ID Tiket:</span>
                    <span class="font-bold text-slate-800">{{ letterData?.ticket_number || '-' }}</span>
                  </div>
                  <div class="flex justify-between text-slate-500">
                    <span>Hash TTE:</span>
                    <span class="font-bold text-slate-800">{{ letterData?.tte?.document_hash || 'SHA256-VALID' }}</span>
                  </div>
                </div>
              </div>

              <!-- Kolom Kanan: Kotak Tanda Tangan Elektronik (TTE) -->
              <div class="font-sans text-xs w-full sm:w-80 order-1 sm:order-2">
                <div class="text-left sm:text-right text-slate-700 mb-1.5 text-xs">
                  <p>Ditetapkan di: Desa Mandiri Sejahtera</p>
                  <p>Pada tanggal: <span class="font-medium text-slate-900">{{ letterData?.tanggal_terbit || '24 November 2026' }}</span></p>
                </div>

                <p class="font-bold text-slate-950 uppercase text-xs tracking-wider text-left sm:text-right mb-2">
                  {{ letterData?.pejabat?.jabatan || 'Kepala Desa Mandiri Sejahtera' }}
                </p>

                <!-- BADGE & KOTAK TANDA TANGAN DIGITAL (TTE) RESMI BSrE -->
                <div class="p-3 rounded-2xl border-2 border-slate-900 bg-gradient-to-br from-slate-50 via-white to-blue-50/40 shadow-xs relative overflow-hidden">
                  <!-- Watermark Lambang Digital di Background Box -->
                  <div class="absolute -right-4 -bottom-4 w-24 h-24 text-blue-900/5 pointer-events-none select-none">
                    <svg viewBox="0 0 100 100" class="w-full h-full fill-current">
                      <path d="M50 10 L85 25 C85 65 50 95 50 95 C50 95 15 65 15 25 Z"/>
                    </svg>
                  </div>

                  <div class="flex items-center gap-3 relative z-10">
                    <!-- QR Code Otentikasi TTE Digital -->
                    <div class="w-18 h-18 bg-white p-1 rounded-xl border-2 border-slate-900 shrink-0 flex items-center justify-center shadow-xs">
                      <svg viewBox="0 0 100 100" class="w-full h-full text-slate-950 fill-current">
                        <path d="M0 0h30v30H0zm5 5v20h20V5zm5 5h10v10H10zm60-10h30v30H70zm5 5v20h20V5zm5 5h10v10H80zM0 70h30v30H0zm5 5v20h20V75zm5 5h10v10H10zm40-70h10v10H50zm10 10h10v10H60zm-20 10h10v10H40zm10 10h10v10H50zm-10 10h10v10H40zm20 0h10v10H60zm10 10h10v10H70zm10-10h10v10H80zm0 20h10v10H80zm-40 0h10v10H40zm10 10h10v10H50zm10 10h10v10H60zm-30-20h10v10H30zm0 10h10v10H30zm60-10h10v20H90zm-10 10h10v10H80z"/>
                      </svg>
                    </div>

                    <!-- Teks Spesifikasi Sertifikat TTE -->
                    <div class="text-left space-y-0.5 min-w-0">
                      <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100/80 text-emerald-800 text-[9px] font-extrabold uppercase tracking-wide border border-emerald-300">
                        <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>TTE Sah Bersertifikat</span>
                      </div>
                      
                      <p class="text-[9px] text-slate-500 font-medium leading-none pt-0.5">Ditandatangani secara elektronik oleh:</p>
                      <p class="font-extrabold text-slate-950 uppercase text-[11px] leading-tight truncate">
                        {{ letterData?.pejabat?.nama || 'H. AHMAD SYARIFUDDIN, S.IP' }}
                      </p>
                      <p class="text-[9px] text-slate-600 font-mono leading-none">
                        NIP. {{ letterData?.pejabat?.nip || '19780512 200604 1 008' }}
                      </p>
                      <p class="text-[8px] text-blue-900 font-semibold pt-0.5 leading-none">
                        Balai Sertifikasi Elektronik (BSrE) - BSSN
                      </p>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- CATATAN KAKI LEGALITAS UU ITE (Footer Dokumen Elektronik Resmi) -->
            <div class="mt-8 pt-3 border-t-2 border-slate-300 text-[8.5px] font-sans text-slate-500 space-y-1">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <span class="font-bold text-slate-800">
                  Catatan Legalitas Tanda Tangan Elektronik (UU ITE No. 11/2008 & PP No. 71/2019):
                </span>
                <span class="font-mono text-[8px] text-slate-400">Verifikasi Dokumen: {{ letterData?.ticket_number }}</span>
              </div>
              <p class="leading-relaxed text-slate-600">
                Dokumen ini telah ditandatangani secara elektronik resmi menggunakan sertifikat elektronik yang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN). Surat keterangan ini sah berkekuatan hukum serta tidak memerlukan tanda tangan basah dan cap stempel manual. Keaslian dokumen dapat diverifikasi secara daring melalui portal pelayanan resmi desa dengan memindai kode QR di atas.
              </p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
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
});

defineEmits(['close']);

const handlePrint = () => {
  window.print();
};
</script>

<style scoped>
@media print {
  @page {
    size: A4 portrait;
    margin: 15mm 15mm 15mm 15mm;
  }

  /* Sembunyikan elemen latar dan modal backdrop */
  body * {
    visibility: hidden;
  }
  
  /* Hanya tampilkan kertas surat */
  #official-letter-paper, #official-letter-paper * {
    visibility: visible;
  }

  #official-letter-paper {
    position: absolute;
    left: 0;
    top: 0;
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
    border: none !important;
    background: transparent !important;
  }
}
</style>
