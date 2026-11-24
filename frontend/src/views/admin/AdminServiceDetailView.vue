<template>
  <div class="space-y-6 max-w-5xl">
    <!-- Breadcrumb & Back -->
    <div>
      <RouterLink to="/admin/layanan" class="text-xs font-semibold text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
        ← Kembali ke Daftar Permohonan
      </RouterLink>
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Detail Pengajuan Surat</span>
          <h2 class="text-2xl font-extrabold text-slate-900 font-mono">{{ detail?.ticket_number || 'Loading...' }}</h2>
        </div>
        <div v-if="detail">
          <StatusBadge :status="detail.status" />
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="h-64 bg-white rounded-2xl border border-slate-200 animate-pulse"></div>

    <div v-else-if="detail" class="space-y-6">
      <!-- Banner Keamanan Dekripsi Admin -->
      <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-950 text-xs">
        <span class="text-base">🔓</span>
        <div>
          <span class="font-bold block">Data Warga Didekripsi Khusus Sesi Administrator Terverifikasi</span>
          <span class="text-emerald-800 text-[11px]">Kunci privat server telah mendekripsi data NIK, No KK, dan kontak untuk keperluan verifikasi berkas pelayanan.</span>
        </div>
      </div>

      <!-- Card Identitas Warga (Decrypted PII) -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100 flex items-center justify-between">
          <span>Identitas Pemohon</span>
          <span class="text-xs font-normal text-slate-400 capitalize">{{ detail.service_type?.name }}</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
          <div>
            <span class="text-slate-400 block mb-1">Nama Lengkap (Sesuai KTP):</span>
            <span class="text-sm font-bold text-slate-900">{{ detail.full_name }}</span>
          </div>

          <div>
            <span class="text-slate-400 block mb-1">Nomor Induk Kependudukan (NIK):</span>
            <span class="text-sm font-bold text-slate-900 font-mono tracking-wider">{{ detail.nik }}</span>
          </div>

          <div>
            <span class="text-slate-400 block mb-1">Nomor Kartu Keluarga (No KK):</span>
            <span class="text-sm font-bold text-slate-900 font-mono tracking-wider">{{ detail.kk_number }}</span>
          </div>

          <div>
            <span class="text-slate-400 block mb-1">Nomor WhatsApp / HP:</span>
            <div class="flex items-center gap-2">
              <span class="text-sm font-bold text-slate-900">{{ detail.phone_number }}</span>
              <a
                :href="`https://wa.me/${cleanPhone(detail.phone_number)}`"
                target="_blank"
                class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-[10px]"
              >
                Chat WA →
              </a>
            </div>
          </div>

          <div class="sm:col-span-2">
            <span class="text-slate-400 block mb-1">Alamat Lengkap di Desa:</span>
            <p class="text-sm font-medium text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100">
              {{ detail.address }}
            </p>
          </div>

          <div class="sm:col-span-2">
            <span class="text-slate-400 block mb-1">Keperluan / Keterangan Pengajuan:</span>
            <p class="text-sm font-medium text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100">
              {{ detail.purpose_description || 'Tidak ada keterangan tambahan.' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Card Berkas Lampiran Privat -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
          Berkas Lampiran Privat (KTP/KK)
        </h3>

        <div v-if="detail.documents.length === 0" class="text-xs text-slate-400">
          Tidak ada berkas lampiran yang diunggah.
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-for="doc in detail.documents"
            :key="doc.id"
            class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between"
          >
            <div class="min-w-0 pr-3">
              <span class="text-xs font-bold text-slate-900 block truncate uppercase">{{ doc.document_type }}</span>
              <span class="text-[11px] text-slate-500 block truncate">{{ doc.original_name }} ({{ formatBytes(doc.file_size) }})</span>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewDoc(doc.id)"
                class="px-2.5 py-1.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg transition-colors shadow-xs"
                title="Buka Preview"
              >
                Lihat
              </button>
              <button
                @click="downloadDoc(doc.id, doc.original_name)"
                class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-colors shadow-xs"
                title="Unduh Berkas"
              >
                Unduh
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Update Status & Catatan Petugas -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-5">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
          Tindak Lanjut & Pembaruan Status
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Ubah Status Pengajuan:</label>
            <select
              v-model="statusUpdate"
              class="w-full text-sm bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:ring-2 focus:ring-slate-900"
            >
              <option value="pending">Menunggu (Pending)</option>
              <option value="diproses">Sedang Diproses (Verifikasi / Pembuatan)</option>
              <option value="selesai">Selesai (Siap Diambil)</option>
              <option value="ditolak">Ditolak (Berkas Tidak Sesuai)</option>
            </select>
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-700 mb-1">
              Catatan untuk Warga (Akan tampil saat warga melacak nomor tiket):
            </label>
            <textarea
              v-model="notesUpdate"
              rows="3"
              placeholder="Contoh: Berkas telah diverifikasi, surat sudah ditandatangani kepala desa dan dapat diambil di loket pelayanan."
              class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:ring-2 focus:ring-slate-900"
            ></textarea>
          </div>
        </div>

        <div v-if="saveMessage" class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl">
          {{ saveMessage }}
        </div>

        <div class="flex items-center justify-end">
          <button
            @click="handleSaveStatus"
            :disabled="isSaving"
            class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md disabled:opacity-50"
          >
            {{ isSaving ? 'Menyimpan...' : 'Simpan Pembaruan Status' }}
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
const statusUpdate = ref('pending');
const notesUpdate = ref('');
const saveMessage = ref('');

const cleanPhone = (phone) => {
  if (!phone) return '';
  let p = phone.replace(/[^0-9]/g, '');
  if (p.startsWith('0')) p = '62' + p.substring(1);
  return p;
};

const formatBytes = (bytes) => {
  if (!bytes) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
};

const loadDetail = async () => {
  isLoading.value = true;
  try {
    const res = await adminService.getServiceDetail(route.params.id);
    detail.value = res;
    statusUpdate.value = res.status;
    notesUpdate.value = res.admin_notes || '';
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const handleSaveStatus = async () => {
  isSaving.value = true;
  saveMessage.value = '';
  try {
    const res = await adminService.updateServiceStatus(detail.value.id, statusUpdate.value, notesUpdate.value);
    if (res.success) {
      detail.value = res.data;
      saveMessage.value = 'Pembaruan status dan catatan untuk warga berhasil disimpan!';
      setTimeout(() => (saveMessage.value = ''), 3000);
    }
  } catch (err) {
    alert('Gagal memperbarui status.');
  } finally {
    isSaving.value = false;
  }
};

const previewDoc = async (id) => {
  await adminService.previewDocument(id);
};

const downloadDoc = async (id, name) => {
  await adminService.downloadDocument(id, name);
};

onMounted(() => {
  loadDetail();
});
</script>

