<template>
  <div class="py-12 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb & Header -->
      <div class="mb-8">
        <RouterLink to="/" class="text-xs font-semibold text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
          ← Kembali ke Beranda
        </RouterLink>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Pengajuan Surat Online</h1>
        <p class="text-sm text-slate-500 mt-1">Isi formulir dengan data resmi sesuai KTP dan Kartu Keluarga Anda.</p>
      </div>

      <!-- Shield Informasi Enkripsi -->
      <div class="mb-8">
        <EncryptionShield />
      </div>

      <!-- Success Modal Tiket -->
      <div v-if="submittedTicket" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 text-center animate-in fade-in zoom-in duration-200">
          <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>

          <h3 class="text-xl font-extrabold text-slate-900">Pengajuan Berhasil Dikirim!</h3>
          <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Data Anda telah terenkripsi aman dan masuk ke sistem antrean pelayanan desa. Simpan nomor tiket berikut untuk melacak status permohonan.
          </p>

          <div class="my-6 p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
            <div class="text-left">
              <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Nomor Tiket Anda</span>
              <span class="text-lg font-mono font-extrabold text-slate-900 select-all">{{ submittedTicket }}</span>
            </div>
            <button
              @click="copyTicket"
              class="px-3.5 py-2 text-xs font-bold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs"
            >
              {{ copied ? 'Tersalin ✓' : 'Salin Tiket' }}
            </button>
          </div>

          <div class="flex items-center gap-3">
            <RouterLink
              :to="{ path: '/lacak', query: { ticket: submittedTicket } }"
              class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-xs"
            >
              Lacak Status Sekarang
            </RouterLink>
            <button
              @click="resetForm"
              class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors"
            >
              Buat Pengajuan Baru
            </button>
          </div>
        </div>
      </div>

      <!-- Main Form Card -->
      <form @submit.prevent="handleSubmit" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-8">
        <!-- 1. Pilihan Jenis Surat -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            1. Pilih Jenis Surat <span class="text-rose-500">*</span>
          </label>
          <div v-if="loadingTypes" class="h-11 bg-slate-100 animate-pulse rounded-xl"></div>
          <select
            v-else
            v-model="form.service_type_id"
            required
            class="w-full text-sm bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
          >
            <option value="" disabled>-- Pilih jenis surat yang dibutuhkan --</option>
            <option v-for="type in serviceTypes" :key="type.id" :value="type.id">
              {{ type.name }} ({{ type.code }})
            </option>
          </select>

          <!-- Keterangan surat terpilih -->
          <div v-if="selectedType" class="mt-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600">
            <p class="font-semibold text-slate-800 mb-1">{{ selectedType.description }}</p>
            <p class="text-slate-500">Berkas yang diperlukan: <span class="font-medium text-slate-700">{{ requiredDocsList }}</span></p>
          </div>
        </div>

        <!-- 2. Data Identitas Pemohon (Sensitive Fields) -->
        <div class="pt-4 border-t border-slate-100">
          <div class="flex items-center justify-between mb-4">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
              2. Data Identitas Pemohon (Otomatis Terenkripsi) <span class="text-rose-500">*</span>
            </label>
            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
              🔒 In-Transit AES-256
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span></label>
              <input
                v-model="form.nik"
                type="text"
                maxlength="16"
                required
                pattern="[0-9]{16}"
                placeholder="16 digit angka NIK"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-mono"
              />
              <span class="text-[10px] text-slate-400 mt-1 block">Contoh: 3201123456789012</span>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Kartu Keluarga (No KK) <span class="text-rose-500">*</span></label>
              <input
                v-model="form.kk_number"
                type="text"
                maxlength="16"
                required
                pattern="[0-9]{16}"
                placeholder="16 digit nomor KK"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-mono"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Nama Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
              <input
                v-model="form.full_name"
                type="text"
                required
                placeholder="Nama lengkap pemohon"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span></label>
              <input
                v-model="form.phone_number"
                type="tel"
                required
                placeholder="081234567890"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-medium text-slate-600 mb-1">Alamat Lengkap di Desa <span class="text-rose-500">*</span></label>
              <textarea
                v-model="form.address"
                rows="2"
                required
                placeholder="Dusun / RT / RW / No. Rumah"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              ></textarea>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-medium text-slate-600 mb-1">Tujuan / Keperluan Surat <span class="text-rose-500">*</span></label>
              <textarea
                v-model="form.purpose_description"
                rows="2"
                required
                placeholder="Jelaskan untuk keperluan apa surat ini digunakan (misal: syarat pendaftaran beasiswa, administrasi bank, dll.)"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- 3. Unggah Dokumen Lampiran Privat -->
        <div class="pt-4 border-t border-slate-100">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            3. Unggah Dokumen Pendukung (KTP/KK) <span class="text-rose-500">*</span>
          </label>
          <p class="text-xs text-slate-400 mb-4">Format berkas: JPG, PNG, atau PDF (maksimal 2MB per berkas). Disimpan di storage privat.</p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Upload KTP -->
            <div class="border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-2xl p-4 text-center transition-colors bg-slate-50/50">
              <span class="text-xs font-bold text-slate-800 block mb-1">Foto / Scan KTP Asli *</span>
              <input
                type="file"
                accept="image/jpeg,image/png,application/pdf"
                @change="handleKtpChange"
                required
                class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
              />
              <p v-if="ktpFile" class="text-[11px] text-emerald-600 font-semibold mt-2">✓ Terpilih: {{ ktpFile.name }}</p>
            </div>

            <!-- Upload KK -->
            <div class="border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-2xl p-4 text-center transition-colors bg-slate-50/50">
              <span class="text-xs font-bold text-slate-800 block mb-1">Foto / Scan Kartu Keluarga *</span>
              <input
                type="file"
                accept="image/jpeg,image/png,application/pdf"
                @change="handleKkChange"
                required
                class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
              />
              <p v-if="kkFile" class="text-[11px] text-emerald-600 font-semibold mt-2">✓ Terpilih: {{ kkFile.name }}</p>
            </div>
          </div>
        </div>

        <!-- Error Banner -->
        <div v-if="errorMessage" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
          {{ errorMessage }}
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
          <RouterLink to="/" class="px-5 py-3 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
            Batal
          </RouterLink>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg v-if="isSubmitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isSubmitting ? 'Mengenkripsi & Mengirim...' : 'Kirim Permohonan Surat' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { publicService } from '../../services/publicService';
import EncryptionShield from '../../components/common/EncryptionShield.vue';

const route = useRoute();
const serviceTypes = ref([]);
const loadingTypes = ref(true);
const isSubmitting = ref(false);
const errorMessage = ref('');
const submittedTicket = ref('');
const copied = ref(false);

const ktpFile = ref(null);
const kkFile = ref(null);

const form = reactive({
  service_type_id: '',
  nik: '',
  kk_number: '',
  full_name: '',
  phone_number: '',
  address: '',
  purpose_description: '',
});

const selectedType = computed(() => {
  return serviceTypes.value.find((t) => t.id === Number(form.service_type_id));
});

const requiredDocsList = computed(() => {
  if (!selectedType.value?.required_documents) return 'KTP, KK';
  return selectedType.value.required_documents
    .map((d) => (typeof d === 'object' ? d.label : d))
    .join(', ');
});

const handleKtpChange = (e) => {
  const file = e.target.files[0];
  if (file && file.size > 2048 * 1024) {
    alert('Ukuran file KTP melebihi 2MB.');
    e.target.value = '';
    return;
  }
  ktpFile.value = file;
};

const handleKkChange = (e) => {
  const file = e.target.files[0];
  if (file && file.size > 2048 * 1024) {
    alert('Ukuran file KK melebihi 2MB.');
    e.target.value = '';
    return;
  }
  kkFile.value = file;
};

const copyTicket = () => {
  navigator.clipboard.writeText(submittedTicket.value);
  copied.value = true;
  setTimeout(() => (copied.value = false), 2000);
};

const resetForm = () => {
  submittedTicket.value = '';
  form.service_type_id = '';
  form.nik = '';
  form.kk_number = '';
  form.full_name = '';
  form.phone_number = '';
  form.address = '';
  form.purpose_description = '';
  ktpFile.value = null;
  kkFile.value = null;
};

const handleSubmit = async () => {
  errorMessage.value = '';
  isSubmitting.value = true;

  try {
    const files = [];
    const types = [];

    if (ktpFile.value) {
      files.push(ktpFile.value);
      types.push('ktp');
    }
    if (kkFile.value) {
      files.push(kkFile.value);
      types.push('kk');
    }

    const res = await publicService.submitService(form, files, types);

    if (res.success && res.ticket_number) {
      submittedTicket.value = res.ticket_number;
    }
  } catch (err) {
    console.error(err);
    if (err.response?.data?.errors) {
      const errList = Object.values(err.response.data.errors).flat();
      errorMessage.value = errList.join(' • ');
    } else {
      errorMessage.value = err.response?.data?.message || 'Gagal mengirim pengajuan surat. Periksa kembali isian formulir Anda.';
    }
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(async () => {
  try {
    serviceTypes.value = await publicService.getServiceTypes();
    if (route.query.type) {
      form.service_type_id = Number(route.query.type);
    }
  } catch (err) {
    errorMessage.value = 'Gagal memuat jenis layanan.';
  } finally {
    loadingTypes.value = false;
  }
});
</script>

