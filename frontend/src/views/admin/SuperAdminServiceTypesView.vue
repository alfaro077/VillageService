<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200 mb-1">
          <span>👑 Fitur Khusus Superadmin</span>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Katalog Layanan Surat</h2>
        <p class="text-xs text-slate-500 mt-1">Atur jenis surat apa saja yang dapat diajukan oleh warga dan tentukan syarat berkas wajibnya.</p>
      </div>

      <button
        @click="openAddModal"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md shrink-0"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Jenis Surat Baru
      </button>
    </div>

    <!-- Alert Sukses / Gagal -->
    <div v-if="alertMessage" :class="['p-4 rounded-xl text-xs flex items-center justify-between', alertSuccess ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800']">
      <span>{{ alertMessage }}</span>
      <button @click="alertMessage = ''" class="font-bold text-xs">✕</button>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div v-for="i in 4" :key="i" class="h-44 bg-white border border-slate-200 rounded-2xl animate-pulse"></div>
    </div>

    <!-- Grid Kartu Jenis Surat -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div
        v-for="service in services"
        :key="service.id"
        class="bg-white rounded-2xl p-6 border transition-all flex flex-col justify-between"
        :class="service.is_active ? 'border-slate-200 shadow-xs' : 'border-slate-200 bg-slate-50/60 opacity-80'"
      >
        <div>
          <!-- Header Card -->
          <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-2.5">
              <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-800 font-mono font-bold text-xs flex items-center justify-center border border-slate-200">
                {{ service.code }}
              </span>
              <div>
                <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ service.name }}</h3>
                <span class="text-[11px] text-slate-400 font-medium">Total: {{ service.requests_count }} pengajuan warga</span>
              </div>
            </div>

            <!-- Status Switch -->
            <button
              @click="handleToggle(service)"
              :class="[
                'px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase transition-colors border',
                service.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200'
              ]"
              :title="service.is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
            >
              {{ service.is_active ? '● Aktif' : '○ Nonaktif' }}
            </button>
          </div>

          <p class="text-xs text-slate-600 leading-relaxed mb-4">
            {{ service.description || 'Tidak ada deskripsi layanan.' }}
          </p>

          <!-- Syarat Dokumen -->
          <div class="pt-3 border-t border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Syarat Lampiran Dokumen:</span>
            <div class="flex flex-wrap gap-1.5">
              <span
                v-for="(doc, idx) in service.required_documents"
                :key="idx"
                class="text-[11px] bg-slate-50 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium"
              >
                📎 {{ typeof doc === 'object' ? doc.label : doc }}
              </span>
              <span v-if="!service.required_documents || service.required_documents.length === 0" class="text-xs text-slate-400 italic">
                Tanpa dokumen wajib
              </span>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
          <button
            @click="openEditModal(service)"
            class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-xs"
          >
            Edit Layanan
          </button>
          <button
            @click="handleDelete(service)"
            class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50/50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors"
          >
            Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Form (Tambah / Edit) -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">
              {{ isEditing ? 'Edit Jenis Layanan Surat' : 'Tambah Jenis Layanan Surat Baru' }}
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Konfigurasikan kode, nama, dan berkas lampiran yang diminta kepada warga.</p>
          </div>
          <button @click="showModal = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-lg">✕</button>
        </div>

        <form @submit.prevent="saveService" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Kode Surat *</label>
              <input
                v-model="modalForm.code"
                type="text"
                required
                maxlength="20"
                placeholder="SKD, SKTM..."
                class="w-full text-xs font-mono uppercase border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Layanan *</label>
              <input
                v-model="modalForm.name"
                type="text"
                required
                placeholder="Contoh: Surat Keterangan Domisili"
                class="w-full text-xs border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Kegunaan Surat</label>
            <textarea
              v-model="modalForm.description"
              rows="2"
              placeholder="Jelaskan secara singkat kegunaan surat ini..."
              class="w-full text-xs border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800"
            ></textarea>
          </div>

          <!-- Dynamic Syarat Dokumen -->
          <div class="pt-2">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-xs font-bold text-slate-700">Syarat Lampiran Dokumen Wajib</label>
              <button
                type="button"
                @click="addDocumentRow"
                class="text-xs text-emerald-700 font-bold hover:text-emerald-800 flex items-center gap-1"
              >
                + Tambah Syarat Dokumen
              </button>
            </div>

            <div class="space-y-2">
              <div
                v-for="(doc, idx) in modalForm.required_documents"
                :key="idx"
                class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200"
              >
                <input
                  v-model="doc.label"
                  type="text"
                  required
                  placeholder="Nama Berkas (misal: Foto KTP Pemohon)"
                  class="flex-1 text-xs border border-slate-300 rounded-lg px-3 py-1.5 bg-white text-slate-800"
                />
                <input
                  v-model="doc.type"
                  type="text"
                  required
                  placeholder="Kode (misal: ktp)"
                  class="w-28 text-xs font-mono border border-slate-300 rounded-lg px-2.5 py-1.5 bg-white text-slate-800"
                />
                <button
                  type="button"
                  @click="removeDocumentRow(idx)"
                  class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold"
                  title="Hapus baris"
                >
                  ✕
                </button>
              </div>
            </div>
          </div>

          <!-- Status Aktif -->
          <div class="flex items-center gap-2 pt-2">
            <input
              id="isActiveCheck"
              v-model="modalForm.is_active"
              type="checkbox"
              class="w-4 h-4 text-slate-900 border-slate-300 rounded-sm focus:ring-slate-900"
            />
            <label for="isActiveCheck" class="text-xs font-bold text-slate-700">
              Aktifkan layanan ini langsung di portal publik warga
            </label>
          </div>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSaving"
              class="px-6 py-2.5 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-md disabled:opacity-50"
            >
              {{ isSaving ? 'Menyimpan...' : 'Simpan Layanan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { superAdminService } from '../../services/superAdminService';

const isLoading = ref(true);
const isSaving = ref(false);
const services = ref([]);
const alertMessage = ref('');
const alertSuccess = ref(true);

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const modalForm = reactive({
  code: '',
  name: '',
  description: '',
  required_documents: [],
  is_active: true,
});

const loadServices = async () => {
  isLoading.value = true;
  try {
    services.value = await superAdminService.getServiceTypes();
  } catch (err) {
    alertMessage.value = 'Gagal memuat jenis layanan.';
    alertSuccess.value = false;
  } finally {
    isLoading.value = false;
  }
};

const openAddModal = () => {
  isEditing.value = false;
  editingId.value = null;
  modalForm.code = '';
  modalForm.name = '';
  modalForm.description = '';
  modalForm.required_documents = [
    { type: 'ktp', label: 'Foto / Scan KTP Asli' },
    { type: 'kk', label: 'Foto / Scan Kartu Keluarga' },
  ];
  modalForm.is_active = true;
  showModal.value = true;
};

const openEditModal = (service) => {
  isEditing.value = true;
  editingId.value = service.id;
  modalForm.code = service.code;
  modalForm.name = service.name;
  modalForm.description = service.description || '';
  modalForm.required_documents = Array.isArray(service.required_documents)
    ? JSON.parse(JSON.stringify(service.required_documents))
    : [];
  modalForm.is_active = !!service.is_active;
  showModal.value = true;
};

const addDocumentRow = () => {
  modalForm.required_documents.push({ type: 'dokumen', label: 'Nama Dokumen' });
};

const removeDocumentRow = (idx) => {
  modalForm.required_documents.splice(idx, 1);
};

const saveService = async () => {
  isSaving.value = true;
  try {
    if (isEditing.value) {
      await superAdminService.updateServiceType(editingId.value, modalForm);
      alertMessage.value = `Layanan '${modalForm.name}' berhasil diperbarui.`;
    } else {
      await superAdminService.createServiceType(modalForm);
      alertMessage.value = `Layanan baru '${modalForm.name}' berhasil ditambahkan ke portal.`;
    }
    alertSuccess.value = true;
    showModal.value = false;
    await loadServices();
  } catch (err) {
    alertMessage.value = err.response?.data?.message || 'Gagal menyimpan jenis layanan.';
    alertSuccess.value = false;
  } finally {
    isSaving.value = false;
  }
};

const handleToggle = async (service) => {
  try {
    const res = await superAdminService.toggleServiceType(service.id);
    service.is_active = res.data.is_active;
    alertMessage.value = res.message;
    alertSuccess.value = true;
  } catch (err) {
    alert('Gagal mengubah status layanan.');
  }
};

const handleDelete = async (service) => {
  if (confirm(`Yakin ingin menghapus jenis layanan '${service.name}'?`)) {
    try {
      const res = await superAdminService.deleteServiceType(service.id);
      alertMessage.value = res.message;
      alertSuccess.value = true;
      await loadServices();
    } catch (err) {
      alertMessage.value = err.response?.data?.message || 'Gagal menghapus layanan.';
      alertSuccess.value = false;
    }
  }
};

onMounted(() => {
  loadServices();
});
</script>

