<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Permohonan Surat Online</h2>
        <p class="text-xs text-slate-500 mt-1">Daftar seluruh berkas permohonan surat warga beserta status verifikasinya.</p>
      </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
      <!-- Search Input (Ticket / NIK Blind Index) -->
      <div class="relative flex-1 w-full sm:max-w-md">
        <input
          v-model="searchQuery"
          @keyup.enter="handleSearch"
          type="text"
          placeholder="Cari berdasarkan Nomor Tiket atau NIK 16 digit..."
          class="w-full text-xs border border-slate-300 rounded-xl pl-9 pr-4 py-2.5 text-slate-800 focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all font-mono"
        />
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <!-- Status Filter Tabs -->
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

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
      <div v-if="isLoading" class="p-12 text-center text-xs text-slate-400">
        Memuat data pengajuan surat...
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3.5">Nomor Tiket</th>
              <th class="px-6 py-3.5">Jenis Layanan</th>
              <th class="px-6 py-3.5">Nama Pemohon (Didekripsi)</th>
              <th class="px-6 py-3.5">NIK (Didekripsi)</th>
              <th class="px-6 py-3.5">Status</th>
              <th class="px-6 py-3.5">Tanggal Masuk</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-if="items.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                Tidak ada data permohonan yang sesuai dengan filter.
              </td>
            </tr>
            <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ item.ticket_number }}</td>
              <td class="px-6 py-4 font-semibold text-slate-800">{{ item.service_type?.name }}</td>
              <td class="px-6 py-4 font-medium text-slate-900">{{ item.full_name }}</td>
              <td class="px-6 py-4 font-mono text-slate-600">{{ item.nik }}</td>
              <td class="px-6 py-4"><StatusBadge :status="item.status" /></td>
              <td class="px-6 py-4 text-slate-400">{{ formatDate(item.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <RouterLink
                  :to="`/admin/layanan/${item.id}`"
                  class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition-colors inline-flex items-center gap-1 shadow-xs"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Buka & Dekripsi
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div v-if="pagination.last_page > 1" class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span>Menampilkan halaman {{ pagination.current_page }} dari {{ pagination.last_page }} (Total {{ pagination.total }} data)</span>
        <div class="flex items-center gap-1">
          <button
            :disabled="pagination.current_page <= 1"
            @click="changePage(pagination.current_page - 1)"
            class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-40"
          >
            Sebelumnya
          </button>
          <button
            :disabled="pagination.current_page >= pagination.last_page"
            @click="changePage(pagination.current_page + 1)"
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
import { RouterLink } from 'vue-router';
import { adminService } from '../../services/adminService';
import StatusBadge from '../../components/common/StatusBadge.vue';

const isLoading = ref(true);
const items = ref([]);
const pagination = ref({});
const searchQuery = ref('');
const activeStatus = ref('');

const statusFilters = [
  { label: 'Semua Status', value: '' },
  { label: 'Menunggu', value: 'pending' },
  { label: 'Diproses', value: 'diproses' },
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

const loadData = async (page = 1) => {
  isLoading.value = true;
  try {
    const params = { page };
    if (activeStatus.value) params.status = activeStatus.value;
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();

    const res = await adminService.getServices(params);
    items.value = res.data || [];
    pagination.value = res.pagination || {};
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const selectStatus = (st) => {
  activeStatus.value = st;
  loadData(1);
};

const handleSearch = () => {
  loadData(1);
};

const changePage = (page) => {
  loadData(page);
};

onMounted(() => {
  loadData();
});
</script>

