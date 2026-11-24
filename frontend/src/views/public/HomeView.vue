<template>
  <div>
    <!-- Hero Section Bersih, Modern, Terpercaya -->
    <section class="relative bg-gradient-to-b from-emerald-50/50 via-white to-white py-16 sm:py-24 border-b border-slate-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-100/80 text-emerald-800 mb-6">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            Layanan Administrasi Mandiri 100% Digital & Terenkripsi
          </div>

          <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight sm:leading-tight">
            Urus Dokumen & Surat Desa dari Rumah, <span class="text-emerald-600">Lebih Cepat & Aman</span>
          </h1>

          <p class="mt-5 text-base sm:text-lg text-slate-600 leading-relaxed">
            Portal resmi pelayanan kependudukan Desa Mandiri Sejahtera. Ajukan surat permohonan, sampaikan aspirasi/pengaduan, dan pantau status pemrosesan secara *real-time* tanpa antre.
          </p>

          <!-- Input Cepat Lacak Tiket -->
          <div class="mt-8 max-w-xl mx-auto bg-white p-2 rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-200 flex items-center gap-2">
            <div class="pl-3 text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="quickTicket"
              @keyup.enter="handleQuickTrack"
              type="text"
              placeholder="Punya nomor tiket? Contoh: SRV-202611-ABC123"
              class="flex-1 text-sm bg-transparent border-0 focus:ring-0 text-slate-800 placeholder-slate-400 py-2.5 font-mono uppercase"
            />
            <button
              @click="handleQuickTrack"
              class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors shrink-0 shadow-xs"
            >
              Lacak Status
            </button>
          </div>

          <!-- Quick Action Buttons -->
          <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <RouterLink
              to="/layanan"
              class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all shadow-sm"
            >
              <span>Ajukan Permohonan Surat</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </RouterLink>

            <RouterLink
              to="/pengaduan"
              class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-semibold transition-all"
            >
              <span>Pengaduan & Aspirasi Warga</span>
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Fitur Keamanan Warga Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
      <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-md shadow-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-900">Perlindungan Privasi Tingkat Tinggi</h3>
            <p class="text-xs text-slate-500 mt-0.5">NIK, Nomor KK, & Berkas dienkripsi dengan standar kriptografi AES-256 dan RSA Asimetris.</p>
          </div>
        </div>
        <div class="flex items-center gap-6 text-xs text-slate-600 font-semibold shrink-0">
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            Enkripsi In-Transit
          </span>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            Enkripsi At-Rest (Database)
          </span>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            No PII Leaks saat Lacak
          </span>
        </div>
      </div>
    </section>

    <!-- Katalog Layanan Surat Online -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Pelayanan Mandiri</span>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Layanan Administrasi Surat Kependudukan</h2>
          <p class="text-sm text-slate-500 mt-2">Pilih jenis surat yang Anda butuhkan dan isi formulir dalam hitungan menit.</p>
        </div>
        <RouterLink
          to="/layanan"
          class="mt-4 md:mt-0 text-sm font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1"
        >
          Lihat Semua Formulir
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </RouterLink>
      </div>

      <div v-if="loadingTypes" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="h-48 bg-slate-100 animate-pulse rounded-2xl"></div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="service in serviceTypes"
          :key="service.id"
          class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-50 transition-all flex flex-col justify-between group"
        >
          <div>
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 group-hover:bg-emerald-50 group-hover:text-emerald-700 flex items-center justify-center font-bold text-xs transition-colors border border-slate-100">
              {{ service.code }}
            </div>
            <h3 class="text-base font-bold text-slate-900 mt-4 group-hover:text-emerald-600 transition-colors">
              {{ service.name }}
            </h3>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
              {{ service.description }}
            </p>

            <div class="mt-4 pt-4 border-t border-slate-100">
              <span class="text-[11px] font-semibold text-slate-400 block mb-1.5 uppercase tracking-wider">Syarat Lampiran:</span>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="(doc, idx) in service.required_documents"
                  :key="idx"
                  class="text-[11px] bg-slate-50 text-slate-600 px-2 py-0.5 rounded-md border border-slate-100 font-medium"
                >
                  {{ typeof doc === 'object' ? doc.label : doc }}
                </span>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-2">
            <RouterLink
              :to="{ path: '/layanan', query: { type: service.id } }"
              class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold text-slate-800 bg-slate-50 hover:bg-emerald-600 hover:text-white transition-all border border-slate-200 hover:border-emerald-600"
            >
              Ajukan Surat Ini
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Alur Kerja 4 Langkah Mudah -->
    <section class="bg-slate-50/70 border-y border-slate-100 py-16 sm:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
          <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Mudah & Transparan</span>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Cara Mengajukan Surat Online</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
            <span class="text-3xl font-black text-slate-200">01</span>
            <h4 class="text-sm font-bold text-slate-900 mt-2">Pilih Layanan</h4>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Tentukan jenis dokumen atau surat yang Anda butuhkan sesuai keperluan Anda.</p>
          </div>

          <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
            <span class="text-3xl font-black text-slate-200">02</span>
            <h4 class="text-sm font-bold text-slate-900 mt-2">Isi Data & Dokumen</h4>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Lengkapi data diri dan unggah foto/scan berkas KTP & KK yang diminta.</p>
          </div>

          <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
            <span class="text-3xl font-black text-slate-200">03</span>
            <h4 class="text-sm font-bold text-slate-900 mt-2">Dapatkan Nomor Tiket</h4>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Sistem memberikan kode pelacakan unik untuk memantau status secara langsung.</p>
          </div>

          <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
            <span class="text-3xl font-black text-slate-200">04</span>
            <h4 class="text-sm font-bold text-slate-900 mt-2">Ambil Dokumen</h4>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Setelah status dinyatakan Selesai, dokumen resmi siap diambil di kantor balai desa.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pengaduan & Aspirasi Call to Action -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
      <div class="rounded-3xl bg-gradient-to-r from-slate-900 to-slate-800 text-white p-8 sm:p-12 shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="max-w-xl">
          <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Aspirasi & Keluhan</span>
          <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Ada Kendala Fasilitas atau Layanan Desa?</h2>
          <p class="text-sm text-slate-300 mt-3 leading-relaxed">
            Laporkan kerusakan jalan, fasilitas umum, kendala bansos, atau sampaikan saran membangun demi kemajuan desa kita bersama. Laporan Anda ditindaklanjuti secara profesional.
          </p>
        </div>
        <RouterLink
          to="/pengaduan"
          class="shrink-0 px-8 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-sm font-bold rounded-xl transition-colors shadow-lg shadow-emerald-500/20"
        >
          Kirim Pengaduan Sekarang
        </RouterLink>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { publicService } from '../../services/publicService';

const router = useRouter();
const quickTicket = ref('');
const serviceTypes = ref([]);
const loadingTypes = ref(true);

const handleQuickTrack = () => {
  if (quickTicket.value.trim()) {
    router.push({ path: '/lacak', query: { ticket: quickTicket.value.trim() } });
  }
};

onMounted(async () => {
  try {
    serviceTypes.value = await publicService.getServiceTypes();
  } catch (err) {
    console.error('Gagal mengambil jenis surat:', err);
  } finally {
    loadingTypes.value = false;
  }
});
</script>

