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
      <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center gap-3 text-slate-900 text-xs">
        <span class="text-base">🔓</span>
        <div>
          <span class="font-bold block">Data Warga Didekripsi Khusus Sesi Administrator Terverifikasi</span>
          <span class="text-slate-600 text-[11px]">Kunci privat server telah mendekripsi data NIK, No KK, dan kontak untuk keperluan verifikasi berkas pelayanan.</span>
        </div>
      </div>

      <!-- Card Surat Resmi Diterbitkan & Kirim WhatsApp -->
      <div v-if="detail.status === 'selesai' || detail.letter_number" class="bg-gradient-to-r from-slate-900 via-slate-900 to-blue-950 text-white rounded-2xl shadow-md p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 border border-slate-800">
        <div class="space-y-1.5 text-center md:text-left">
          <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            Surat Resmi Selesai Diterbitkan
          </div>
          <h3 class="text-lg font-bold font-mono tracking-wide text-white">
            No. Surat: {{ detail.letter_number || '470/.../DMS/2026' }}
          </h3>
          <p class="text-xs text-slate-300 max-w-lg">
            Dokumen resmi digital dengan kop surat desa, stempel resmi, dan QR code verifikasi sah siap dicetak dan dikirimkan langsung ke nomor WhatsApp warga.
          </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
          <button
            @click="openLetterModal"
            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Lihat / Cetak Surat</span>
          </button>

          <a
            v-if="detail.whatsapp?.whatsapp_url"
            :href="detail.whatsapp.whatsapp_url"
            target="_blank"
            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer"
          >
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span>Kirim WhatsApp</span>
          </a>
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
              <span class="text-sm font-bold text-slate-900 font-mono">{{ detail.phone_number }}</span>
              <a
                :href="detail.whatsapp?.whatsapp_url || `https://wa.me/${cleanPhone(detail.phone_number)}`"
                target="_blank"
                class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-[11px] inline-flex items-center gap-1 shadow-xs transition-colors"
              >
                <span>Chat WhatsApp</span>
                <span>↗</span>
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

    <!-- Modal Cetak Surat Resmi Desa & WhatsApp -->
    <OfficialLetterModal
      :isOpen="isLetterModalOpen"
      :letterData="letterData"
      :whatsappUrl="detail?.whatsapp?.whatsapp_url"
      @close="isLetterModalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { adminService } from '../../services/adminService';
import StatusBadge from '../../components/common/StatusBadge.vue';
import OfficialLetterModal from '../../components/common/OfficialLetterModal.vue';

const route = useRoute();
const isLoading = ref(true);
const isSaving = ref(false);
const detail = ref(null);
const statusUpdate = ref('pending');
const notesUpdate = ref('');
const saveMessage = ref('');

const isLetterModalOpen = ref(false);
const letterData = ref(null);
const isLetterLoading = ref(false);

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

const openLetterModal = async () => {
  if (!detail.value) return;
  isLetterLoading.value = true;
  try {
    const data = await adminService.getServiceLetter(detail.value.id);
    letterData.value = data;
    isLetterModalOpen.value = true;
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal memuat dokumen surat resmi.');
  } finally {
    isLetterLoading.value = false;
  }
};

const handleSaveStatus = async () => {
  isSaving.value = true;
  saveMessage.value = '';
  try {
    const res = await adminService.updateServiceStatus(detail.value.id, statusUpdate.value, notesUpdate.value);
    if (res.success) {
      detail.value = res.data;
      if (res.data.status === 'selesai') {
        saveMessage.value = 'Status permohonan diselesaikan! Nomor surat resmi telah diterbitkan dan notifikasi WhatsApp siap dikirim.';
      } else {
        saveMessage.value = 'Pembaruan status dan catatan untuk warga berhasil disimpan!';
      }
      setTimeout(() => (saveMessage.value = ''), 4000);
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

