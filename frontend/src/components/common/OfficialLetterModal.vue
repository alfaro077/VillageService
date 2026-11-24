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
            <h3 class="text-sm font-bold leading-tight">Surat Resmi Diterbitkan</h3>
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
        <div class="bg-white text-slate-900 w-full max-w-2xl p-8 sm:p-12 shadow-md border border-slate-200 print:shadow-none print:border-none print:p-8 print:max-w-none print:w-full font-serif text-sm leading-relaxed">
          
          <!-- KOP SURAT RESMI DESA -->
          <div class="text-center pb-3 border-b-4 border-double border-slate-900 relative">
            <!-- Lambang Garuda / Logo Desa Simbolik -->
            <div class="text-center mb-2">
              <div class="w-14 h-14 mx-auto rounded-full border-2 border-slate-900 flex items-center justify-center font-bold text-xl">
                🏛️
              </div>
            </div>

            <h4 class="text-sm font-bold uppercase tracking-widest text-slate-800">
              {{ letterData?.kop?.pemerintah_kabupaten || 'PEMERINTAH KABUPATEN BOGOR' }}
            </h4>
            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-800">
              {{ letterData?.kop?.kecamatan || 'KECAMATAN MANDIRI JAYA' }}
            </h4>
            <h2 class="text-xl sm:text-2xl font-black uppercase tracking-wide text-slate-950 mt-0.5">
              {{ letterData?.kop?.desa || 'PEMERINTAH DESA MANDIRI SEJAHTERA' }}
            </h2>
            <p class="text-[11px] font-sans text-slate-600 mt-1">
              {{ letterData?.kop?.alamat || 'Jl. Protokol Desa Mandiri No. 01, Mandiri Jaya, Jawa Barat 16720' }}
            </p>
            <p class="text-[10px] font-sans text-slate-500">
              {{ letterData?.kop?.kontak || 'Email: pemdes@mandirisejahtera.desa.id • WhatsApp: 0812-3456-7890' }}
            </p>
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

          <!-- TANDA TANGAN & PENGESAHAN KEPALA DESA -->
          <div class="mt-8 pt-4 flex justify-between items-end">
            <!-- QR Code Validasi Dokumen Digital -->
            <div class="text-left font-sans text-[10px] text-slate-500 max-w-[200px]">
              <div class="w-20 h-20 bg-slate-50 border border-slate-300 p-1.5 rounded-lg mb-1 flex items-center justify-center">
                <!-- SVG QR Code Simbolik -->
                <svg viewBox="0 0 100 100" class="w-full h-full text-slate-900 fill-current">
                  <path d="M0 0h30v30H0zm5 5v20h20V5zm5 5h10v10H10zm60-10h30v30H70zm5 5v20h20V5zm5 5h10v10H80zM0 70h30v30H0zm5 5v20h20V75zm5 5h10v10H10zm40-70h10v10H50zm10 10h10v10H60zm-20 10h10v10H40zm10 10h10v10H50zm-10 10h10v10H40zm20 0h10v10H60zm10 10h10v10H70zm10-10h10v10H80zm0 20h10v10H80zm-40 0h10v10H40zm10 10h10v10H50zm10 10h10v10H60zm-30-20h10v10H30zm0 10h10v10H30zm60-10h10v20H90zm-10 10h10v10H80z"/>
                </svg>
              </div>
              <span class="block font-bold text-slate-700">Kode Verifikasi Sah:</span>
              <span class="font-mono text-[9px] text-slate-600 block truncate">{{ letterData?.ticket_number }}</span>
              <span class="text-[9px] text-slate-400">Pemerintah Desa Mandiri Sejahtera</span>
            </div>

            <!-- Tanda Tangan & Stempel Resmi -->
            <div class="text-center font-sans text-xs w-64 relative">
              <p class="text-slate-700 mb-1">
                Desa Mandiri Sejahtera, {{ letterData?.tanggal_terbit || '24 November 2026' }}
              </p>
              <p class="font-bold text-slate-900 uppercase">
                {{ letterData?.pejabat?.jabatan || 'Kepala Desa Mandiri Sejahtera' }}
              </p>

              <!-- Space TTD & Stempel Bulat Biru -->
              <div class="h-24 my-1 flex items-center justify-center relative">
                <!-- Stempel Cap Basah Desa -->
                <div class="absolute left-6 w-24 h-24 rounded-full border-2 border-dashed border-blue-600/70 text-blue-700 flex flex-col items-center justify-center text-[8px] font-bold uppercase rotate-[-12deg] p-1 pointer-events-none select-none">
                  <span class="text-[7px]">PEMERINTAH DESA</span>
                  <span class="text-[9px] tracking-widest my-0.5">★ MANDIRI ★</span>
                  <span class="text-[7px]">KAB. BOGOR</span>
                </div>

                <!-- Tanda Tangan Simbolik -->
                <div class="font-serif italic text-2xl text-slate-800 rotate-[-5deg] select-none">
                  Ahmad Syarif
                </div>
              </div>

              <!-- Nama Pejabat -->
              <p class="font-bold text-slate-950 underline underline-offset-2 uppercase">
                {{ letterData?.pejabat?.nama || 'H. AHMAD SYARIFUDDIN, S.IP' }}
              </p>
              <p class="text-[10px] text-slate-500 font-mono">
                NIP. {{ letterData?.pejabat?.nip || '19780512 200604 1 008' }}
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
  /* Sembunyikan elemen latar dan modal backdrop */
  body * {
    visibility: hidden;
  }
  
  /* Hanya tampilkan kertas surat */
  .max-w-2xl, .max-w-2xl * {
    visibility: visible;
  }

  .max-w-2xl {
    position: absolute;
    left: 0;
    top: 0;
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
    border: none !important;
  }
}
</style>
