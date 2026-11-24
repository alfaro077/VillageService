<template>
  <div class="py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb & Header -->
      <div class="mb-8 text-center max-w-xl mx-auto">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Lacak Status Pengajuan & Pengaduan</h1>
        <p class="text-sm text-slate-500 mt-2">
          Masukkan nomor tiket yang Anda dapatkan saat mengajukan surat atau pengaduan untuk melihat progres penanganannya.
        </p>
      </div>

      <!-- Input Tiket Box -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm mb-8">
        <form @submit.prevent="handleTrack" class="flex flex-col sm:flex-row items-center gap-3">
          <div class="relative flex-1 w-full">
            <input
              v-model="ticketInput"
              type="text"
              required
              placeholder="Contoh: SRV-202611-ABC123 atau ADU-202611-XYZ789"
              class="w-full text-base font-mono uppercase bg-slate-50 border border-slate-300 rounded-2xl px-5 py-3.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all"
            />
          </div>
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-2xl transition-all shadow-md shadow-blue-600/20 shrink-0 disabled:opacity-50 cursor-pointer"
          >
            {{ isLoading ? 'Mencari...' : 'Periksa Status' }}
          </button>
        </form>

        <div v-if="errorMsg" class="mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
          {{ errorMsg }}
        </div>
      </div>

      <!-- Hasil Tracking Card -->
      <div v-if="result" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6 animate-in fade-in duration-200">
        <!-- Header Hasil -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
          <div>
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Nomor Tiket</span>
            <h2 class="text-xl font-mono font-extrabold text-slate-900">{{ result.ticket_number }}</h2>
          </div>
          <div>
            <StatusBadge :status="result.status" />
          </div>
        </div>

        <!-- Detail Layanan & Waktu -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="text-slate-400 block mb-1">Jenis Layanan / Pengaduan:</span>
            <span class="text-sm font-bold text-slate-800">{{ result.title }}</span>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <span class="text-slate-400 block mb-1">Tanggal Diajukan:</span>
            <span class="text-sm font-bold text-slate-800">{{ formatDate(result.created_at) }}</span>
          </div>
        </div>

        <!-- Catatan Resmi Petugas -->
        <div class="p-5 rounded-2xl border" :class="notesCardClass">
          <h4 class="text-xs font-bold uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Catatan Resmi Petugas Pelayanan Desa:
          </h4>
          <p class="text-sm font-medium mt-1 leading-relaxed">
            {{ result.notes || 'Belum ada catatan tambahan dari petugas. Permohonan Anda sedang dalam antrean verifikasi berkas.' }}
          </p>
        </div>

        <!-- Privacy Shield Notice -->
        <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-200 text-slate-900 text-xs flex items-center gap-3">
          <div class="p-2 bg-blue-600 text-white rounded-xl shrink-0 shadow-xs">
            🔒
          </div>
          <p class="text-slate-600 leading-relaxed">
            <strong class="text-slate-900">Privasi Terlindungi:</strong> Informasi identitas warga (NIK, Nama, dan Alamat) sengaja tidak ditampilkan pada portal lacak publik ini untuk mencegah kebocoran data pribadi Anda kepada pihak lain.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { publicService } from '../../services/publicService';
import StatusBadge from '../../components/common/StatusBadge.vue';

const route = useRoute();
const ticketInput = ref('');
const isLoading = ref(false);
const errorMsg = ref('');
const result = ref(null);

const formatDate = (isoStr) => {
  if (!isoStr) return '-';
  const d = new Date(isoStr);
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const notesCardClass = computed(() => {
  if (result.value?.status === 'selesai') {
    return 'bg-blue-50 border-blue-200 text-blue-950';
  }
  if (result.value?.status === 'ditolak') {
    return 'bg-rose-50 border-rose-200 text-rose-900';
  }
  return 'bg-slate-50 border-slate-200 text-slate-800';
});

const handleTrack = async () => {
  if (!ticketInput.value.trim()) return;
  isLoading.value = true;
  errorMsg.value = '';
  result.value = null;

  try {
    result.value = await publicService.trackTicket(ticketInput.value.trim());
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Nomor tiket tidak ditemukan atau tidak valid.';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  if (route.query.ticket) {
    ticketInput.value = route.query.ticket;
    handleTrack();
  }
});
</script>

