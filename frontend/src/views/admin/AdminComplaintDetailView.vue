<template>
  <div class="space-y-6 max-w-4xl">
    <div>
      <RouterLink to="/admin/pengaduan" class="text-xs font-semibold text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
        ← Kembali ke Daftar Pengaduan
      </RouterLink>
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Detail Pengaduan Warga</span>
          <h2 class="text-2xl font-extrabold text-slate-900 font-mono">{{ detail?.ticket_number || 'Loading...' }}</h2>
        </div>
        <div v-if="detail">
          <StatusBadge :status="detail.status" />
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="h-64 bg-white rounded-2xl border border-slate-200 animate-pulse"></div>

    <div v-else-if="detail" class="space-y-6">
      <!-- Detail Card -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200 inline-block mb-2">
            Kategori: {{ detail.category }}
          </span>
          <h3 class="text-lg font-bold text-slate-900">{{ detail.complaint_title }}</h3>
          <p class="text-xs text-slate-400 mt-1">Diajukan pada: {{ formatDate(detail.created_at) }}</p>
        </div>

        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
          <span class="text-xs font-bold text-slate-700 block mb-1">Isi Rinci Laporan:</span>
          <p class="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">{{ detail.complaint_content }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div>
            <span class="text-slate-400 block mb-0.5">Nama Pelapor:</span>
            <span class="text-sm font-bold text-slate-900">{{ detail.reporter_name }}</span>
          </div>

          <div>
            <span class="text-slate-400 block mb-0.5">Nomor Telepon:</span>
            <span class="text-sm font-bold text-slate-900">{{ detail.reporter_phone || '-' }}</span>
          </div>

          <div class="sm:col-span-2">
            <span class="text-slate-400 block mb-0.5">Lokasi Kejadian:</span>
            <span class="text-sm font-bold text-slate-900">{{ detail.location || 'Tidak disebutkan' }}</span>
          </div>
        </div>

        <!-- Lampiran Bukti Foto -->
        <div v-if="detail.documents && detail.documents.length > 0" class="pt-4 border-t border-slate-100">
          <span class="text-xs font-bold text-slate-800 block mb-3">Foto / Bukti Lampiran:</span>
          <div class="flex flex-wrap gap-3">
            <div
              v-for="doc in detail.documents"
              :key="doc.id"
              class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-3"
            >
              <span class="text-xs text-slate-700 font-medium">{{ doc.original_name }}</span>
              <button
                @click="previewDoc(doc.id)"
                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100"
              >
                Lihat
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Update Status & Tanggapan Admin -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
          Tindak Lanjut & Respon Resmi Desa
        </h3>

        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Status Pengaduan:</label>
          <select
            v-model="statusUpdate"
            class="w-full text-sm bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800"
          >
            <option value="menunggu">Menunggu</option>
            <option value="ditindaklanjuti">Sedang Ditindaklanjuti (Pengecekan Lapangan)</option>
            <option value="selesai">Selesai (Sudah Diperbaiki / Terselesaikan)</option>
            <option value="ditolak">Ditolak (Tidak Valid)</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Tanggapan Resmi untuk Warga:</label>
          <textarea
            v-model="responseUpdate"
            rows="3"
            placeholder="Tuliskan tindakan yang telah diambil oleh perangkat desa..."
            class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800"
          ></textarea>
        </div>

        <div v-if="saveMessage" class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl">
          {{ saveMessage }}
        </div>

        <div class="flex items-center justify-end">
          <button
            @click="handleSave"
            :disabled="isSaving"
            class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md disabled:opacity-50"
          >
            {{ isSaving ? 'Menyimpan...' : 'Simpan Tanggapan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { adminService } from '../../services/adminService';
import StatusBadge from '../../components/common/StatusBadge.vue';

const route = useRoute();
const isLoading = ref(true);
const isSaving = ref(false);
const detail = ref(null);
const statusUpdate = ref('menunggu');
const responseUpdate = ref('');
const saveMessage = ref('');

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

const loadDetail = async () => {
  isLoading.value = true;
  try {
    const res = await adminService.getComplaintDetail(route.params.id);
    detail.value = res;
    statusUpdate.value = res.status;
    responseUpdate.value = res.admin_response || '';
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const handleSave = async () => {
  isSaving.value = true;
  saveMessage.value = '';
  try {
    const res = await adminService.updateComplaintStatus(detail.value.id, statusUpdate.value, responseUpdate.value);
    if (res.success) {
      detail.value = res.data;
      saveMessage.value = 'Tanggapan dan status pengaduan berhasil diperbarui!';
      setTimeout(() => (saveMessage.value = ''), 3000);
    }
  } catch (err) {
    alert('Gagal menyimpan pembaruan.');
  } finally {
    isSaving.value = false;
  }
};

const previewDoc = async (id) => {
  await adminService.previewDocument(id);
};

onMounted(() => {
  loadDetail();
});
</script>

