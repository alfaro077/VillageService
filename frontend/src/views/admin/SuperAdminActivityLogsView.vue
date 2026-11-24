<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200 mb-1">
          <span>👑 Fitur Khusus Superadmin</span>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Audit Trail & Monitoring Aktivitas Sistem</h2>
        <p class="text-xs text-slate-500 mt-1">Pantau seluruh jejak operasional, akses data warga, pembaruan status, dan login admin secara real-time.</p>
      </div>

      <button
        @click="loadLogs(1)"
        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-xs"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Segarkan Log
      </button>
    </div>

    <!-- Metric Cards Monitoring -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Log Tercatat</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="text-3xl font-black text-slate-900">{{ stats.total_logs || 0 }}</span>
          <span class="text-xs font-semibold text-slate-500">Peristiwa Sistem</span>
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Aktivitas Hari Ini</span>
        <div class="flex items-baseline justify-between mt-2">
          <span class="text-3xl font-black text-emerald-600">{{ stats.today_logs || 0 }}</span>
          <span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Real-time</span>
        </div>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Login Petugas Terakhir</span>
        <div class="mt-2 text-xs text-slate-700">
          <div v-if="stats.latest_logins && stats.latest_logins.length > 0" class="truncate font-semibold">
            {{ stats.latest_logins[0].user_name }} ({{ formatDate(stats.latest_logins[0].created_at) }})
          </div>
          <div v-else class="text-slate-400 italic">Belum ada riwayat</div>
        </div>
      </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="relative flex-1 w-full sm:max-w-md">
        <input
          v-model="searchQuery"
          @keyup.enter="loadLogs(1)"
          type="text"
          placeholder="Cari deskripsi, nama petugas, atau IP..."
          class="w-full text-xs border border-slate-300 rounded-xl pl-9 pr-4 py-2.5 text-slate-800 focus:ring-2 focus:ring-slate-900"
        />
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <!-- Action Select Filter -->
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <select
          v-model="selectedAction"
          @change="loadLogs(1)"
          class="text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-700 focus:ring-2 focus:ring-slate-900"
        >
          <option value="">Semua Jenis Aksi</option>
          <option value="LOGIN">LOGIN (Masuk Sistem)</option>
          <option value="LOGOUT">LOGOUT (Keluar Sistem)</option>
          <option value="UPDATE_SERVICE_STATUS">UPDATE_SERVICE_STATUS (Ubah Status Surat)</option>
          <option value="UPDATE_COMPLAINT_STATUS">UPDATE_COMPLAINT_STATUS (Tanggapi Aduan)</option>
          <option value="VIEW_DOCUMENT">VIEW_DOCUMENT (Lihat Berkas Privat)</option>
          <option value="DOWNLOAD_DOCUMENT">DOWNLOAD_DOCUMENT (Unduh Dokumen)</option>
          <option value="CREATE_SERVICE_TYPE">CREATE_SERVICE_TYPE (Tambah Layanan)</option>
          <option value="UPDATE_SERVICE_TYPE">UPDATE_SERVICE_TYPE (Edit Layanan)</option>
          <option value="TOGGLE_SERVICE_TYPE">TOGGLE_SERVICE_TYPE (Status Layanan)</option>
          <option value="CREATE_USER">CREATE_USER (Tambah Petugas)</option>
        </select>
      </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
      <div v-if="isLoading" class="p-12 text-center text-xs text-slate-400">
        Memuat jejak audit log...
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3.5">Waktu</th>
              <th class="px-6 py-3.5">Jenis Aksi</th>
              <th class="px-6 py-3.5">Petugas / Pelaku</th>
              <th class="px-6 py-3.5">Deskripsi Aktivitas</th>
              <th class="px-6 py-3.5">Alamat IP</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-if="logs.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                Tidak ada riwayat aktivitas yang sesuai kriteria pencarian.
              </td>
            </tr>
            <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4 text-slate-500 whitespace-nowrap font-mono text-[11px]">
                {{ formatDate(log.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="['px-2.5 py-1 rounded-md text-[10px] font-bold font-mono tracking-wider', getActionBadge(log.action)]">
                  {{ log.action }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-bold text-slate-900">{{ log.user_name }}</div>
                <span class="text-[10px] uppercase font-semibold text-slate-400">{{ log.user_role }}</span>
              </td>
              <td class="px-6 py-4 text-slate-800 leading-relaxed font-medium">
                {{ log.description }}
              </td>
              <td class="px-6 py-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                {{ log.ip_address }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span>Halaman {{ pagination.current_page }} dari {{ pagination.last_page }} (Total {{ pagination.total }} log)</span>
        <div class="flex items-center gap-1">
          <button
            :disabled="pagination.current_page <= 1"
            @click="loadLogs(pagination.current_page - 1)"
            class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-40"
          >
            Sebelumnya
          </button>
          <button
            :disabled="pagination.current_page >= pagination.last_page"
            @click="loadLogs(pagination.current_page + 1)"
            class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-40"
          >
            Selanjutnya
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { superAdminService } from '../../services/superAdminService';

const isLoading = ref(true);
const logs = ref([]);
const stats = ref({});
const pagination = ref({});
const searchQuery = ref('');
const selectedAction = ref('');

const formatDate = (isoStr) => {
  if (!isoStr) return '-';
  const d = new Date(isoStr);
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  });
};

const getActionBadge = (action) => {
  switch (action) {
    case 'LOGIN':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
    case 'LOGOUT':
      return 'bg-slate-100 text-slate-700 border border-slate-200';
    case 'UPDATE_SERVICE_STATUS':
    case 'UPDATE_COMPLAINT_STATUS':
      return 'bg-blue-50 text-blue-700 border border-blue-200';
    case 'VIEW_DOCUMENT':
    case 'DOWNLOAD_DOCUMENT':
      return 'bg-amber-50 text-amber-700 border border-amber-200';
    case 'CREATE_SERVICE_TYPE':
    case 'CREATE_USER':
      return 'bg-teal-50 text-teal-700 border border-teal-200';
    case 'DELETE_SERVICE_TYPE':
    case 'DELETE_USER':
      return 'bg-rose-50 text-rose-700 border border-rose-200';
    default:
      return 'bg-purple-50 text-purple-700 border border-purple-200';
  }
};

const loadLogs = async (page = 1) => {
  isLoading.value = true;
  try {
    const params = { page };
    if (selectedAction.value) params.action = selectedAction.value;
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();

    const res = await superAdminService.getActivityLogs(params);
    logs.value = res.data || [];
    pagination.value = res.pagination || {};
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const loadStats = async () => {
  try {
    stats.value = await superAdminService.getActivityStats();
  } catch (err) {
    console.error(err);
  }
};

onMounted(() => {
  loadLogs(1);
  loadStats();
});
</script>

