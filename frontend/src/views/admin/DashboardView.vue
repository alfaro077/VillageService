<template>
  <div class="space-y-8">
    <!-- Header Selamat Datang -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Ringkasan Layanan Desa</h2>
        <p class="text-xs text-slate-500 mt-1">Pantau seluruh permohonan surat dan aspirasi warga yang masuk secara real-time.</p>
      </div>
      <button
        @click="loadStats"
        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 transition-colors shrink-0 shadow-xs"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Segarkan Data
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <div v-for="i in 4" :key="i" class="h-28 bg-white border border-slate-200 rounded-2xl animate-pulse"></div>
    </div>

    <!-- Metric Cards -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <!-- Total Pengajuan -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pengajuan Surat</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="text-3xl font-black text-slate-900">{{ summary.services?.total || 0 }}</span>
          <span class="text-xs font-semibold text-slate-500">Berkas Masuk</span>
        </div>
      </div>

      <!-- Menunggu Verifikasi -->
      <div class="bg-white p-5 rounded-2xl border border-amber-200 bg-amber-50/20 shadow-xs">
        <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">Menunggu Verifikasi</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="text-3xl font-black text-amber-600">{{ summary.services?.pending || 0 }}</span>
          <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md">Perlu Diproses</span>
        </div>
      </div>

      <!-- Sedang Diproses -->
      <div class="bg-white p-5 rounded-2xl border border-blue-200 bg-blue-50/20 shadow-xs">
        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block">Sedang Diproses</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="text-3xl font-black text-blue-600">{{ summary.services?.processing || 0 }}</span>
          <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-md">Tindak Lanjut</span>
        </div>
      </div>

      <!-- Selesai Diterbitkan -->
      <div class="bg-white p-5 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Surat Selesai</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="text-3xl font-black text-emerald-600">{{ summary.services?.completed || 0 }}</span>
          <span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Siap Diambil</span>
        </div>
      </div>
    </div>

    <!-- Ringkasan Pengaduan Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-base">
          📢
        </div>
        <div>
          <h3 class="text-sm font-bold text-slate-900">Total Pengaduan & Aspirasi Warga</h3>
          <p class="text-xs text-slate-500 mt-0.5">Keluhan masyarakat mengenai fasilitas umum dan bantuan sosial.</p>
        </div>
      </div>
      <div class="flex items-center gap-6 text-center">
        <div>
          <span class="text-xs text-slate-400 block font-semibold">Total Laporan</span>
          <span class="text-xl font-black text-slate-900">{{ summary.complaints?.total || 0 }}</span>
        </div>
        <div class="border-l border-slate-200 pl-6">
          <span class="text-xs text-amber-600 block font-semibold">Menunggu Respon</span>
          <span class="text-xl font-black text-amber-600">{{ summary.complaints?.waiting || 0 }}</span>
        </div>
        <div class="border-l border-slate-200 pl-6">
          <span class="text-xs text-emerald-600 block font-semibold">Selesai</span>
          <span class="text-xl font-black text-emerald-600">{{ summary.complaints?.completed || 0 }}</span>
        </div>
      </div>
    </div>

    <!-- Tabel Pengajuan Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
      <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-slate-900">5 Permohonan Surat Terbaru</h3>
          <p class="text-xs text-slate-400 mt-0.5">Daftar permohonan yang baru saja dikirim oleh warga desa.</p>
        </div>
        <RouterLink to="/admin/layanan" class="text-xs font-bold text-blue-600 hover:text-blue-700">
          Lihat Semua Permohonan →
        </RouterLink>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3.5">Nomor Tiket</th>
              <th class="px-6 py-3.5">Jenis Surat</th>
              <th class="px-6 py-3.5">Nama Pemohon (Didekripsi)</th>
              <th class="px-6 py-3.5">Status</th>
              <th class="px-6 py-3.5">Waktu Masuk</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-if="recentRequests.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                Belum ada permohonan surat yang masuk.
              </td>
            </tr>
            <tr v-for="req in recentRequests" :key="req.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ req.ticket_number }}</td>
              <td class="px-6 py-4 font-semibold text-slate-800">{{ req.service_name }}</td>
              <td class="px-6 py-4">{{ req.applicant_name }}</td>
              <td class="px-6 py-4"><StatusBadge :status="req.status" /></td>
              <td class="px-6 py-4 text-slate-400">{{ formatDate(req.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <RouterLink
                  :to="`/admin/layanan/${req.id}`"
                  class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] transition-colors"
                >
                  Detail & Tindak Lanjut
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { adminService } from '../../services/adminService';
import StatusBadge from '../../components/common/StatusBadge.vue';

const isLoading = ref(true);
const summary = ref({});
const recentRequests = ref([]);
const recentComplaints = ref([]);

const formatDate = (isoStr) => {
  if (!isoStr) return '-';
  const d = new Date(isoStr);
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const loadStats = async () => {
  isLoading.value = true;
  try {
    const res = await adminService.getDashboardStats();
    summary.value = res.summary || {};
    recentRequests.value = res.recent_requests || [];
    recentComplaints.value = res.recent_complaints || [];
  } catch (err) {
    console.error('Gagal mengambil statistik admin:', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadStats();
});
</script>

