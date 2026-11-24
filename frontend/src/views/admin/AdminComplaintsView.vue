<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengaduan Warga</h2>
      <p class="text-xs text-slate-500 mt-1">Daftar aspirasi, keluhan fasilitas umum, dan laporan warga.</p>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="relative flex-1 w-full sm:max-w-md">
        <input
          v-model="searchQuery"
          @keyup.enter="handleSearch"
          type="text"
          placeholder="Cari berdasarkan Nomor Tiket Pengaduan..."
          class="w-full text-xs border border-slate-300 rounded-xl pl-9 pr-4 py-2.5 text-slate-800 font-mono"
        />
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto">
        <button
          v-for="st in statusFilters"
          :key="st.value"
          @click="selectStatus(st.value)"
          :class="[
            'px-3 py-1.5 rounded-lg text-xs font-bold transition-colors whitespace-nowrap',
            activeStatus === st.value ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100'
          ]"
        >
          {{ st.label }}
        </button>
      </div>
    </div>

    <!-- Table Complaints -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
      <div v-if="isLoading" class="p-12 text-center text-xs text-slate-400">
        Memuat data pengaduan...
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3.5">Nomor Tiket</th>
              <th class="px-6 py-3.5">Kategori</th>
              <th class="px-6 py-3.5">Judul Pengaduan</th>
              <th class="px-6 py-3.5">Pelapor</th>
              <th class="px-6 py-3.5">Status</th>
              <th class="px-6 py-3.5">Tanggal</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-if="items.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                Tidak ada data pengaduan yang ditemukan.
              </td>
            </tr>
            <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ item.ticket_number }}</td>
              <td class="px-6 py-4 font-semibold text-slate-700">{{ item.category }}</td>
              <td class="px-6 py-4 font-bold text-slate-900 max-w-xs truncate">{{ item.complaint_title }}</td>
              <td class="px-6 py-4 text-slate-600">{{ item.reporter_name }}</td>
              <td class="px-6 py-4"><StatusBadge :status="item.status" /></td>
              <td class="px-6 py-4 text-slate-400">{{ formatDate(item.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <RouterLink
                  :to="`/admin/pengaduan/${item.id}`"
                  class="px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] transition-colors"
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
const items = ref([]);
const searchQuery = ref('');
const activeStatus = ref('');

const statusFilters = [
  { label: 'Semua Status', value: '' },
  { label: 'Menunggu', value: 'menunggu' },
  { label: 'Ditindaklanjuti', value: 'ditindaklanjuti' },
  { label: 'Selesai', value: 'selesai' },
  { label: 'Ditolak', value: 'ditolak' },
];

const formatDate = (isoStr) => {
  if (!isoStr) return '-';
  const d = new Date(isoStr);
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
};

const loadData = async () => {
  isLoading.value = true;
  try {
    const params = {};
    if (activeStatus.value) params.status = activeStatus.value;
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();

    const res = await adminService.getComplaints(params);
    items.value = res.data || [];
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const selectStatus = (st) => {
  activeStatus.value = st;
  loadData();
};

const handleSearch = () => {
  loadData();
};

onMounted(() => {
  loadData();
});
</script>

