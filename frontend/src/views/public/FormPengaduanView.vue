<template>
  <div class="py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb & Header -->
      <div class="mb-8">
        <RouterLink to="/" class="text-xs font-semibold text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
          ← Kembali ke Beranda
        </RouterLink>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Portal Pengaduan & Aspirasi Warga</h1>
        <p class="text-sm text-slate-500 mt-1">Sampaikan laporan permasalahan infrastruktur, pelayanan, atau saran untuk kemajuan desa.</p>
      </div>

      <div class="mb-8">
        <EncryptionShield />
      </div>

      <!-- Success Modal Tiket -->
      <div v-if="submittedTicket" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 text-center animate-in fade-in zoom-in duration-200">
          <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>

          <h3 class="text-xl font-extrabold text-slate-900">Laporan Pengaduan Berhasil Dikirim!</h3>
          <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Terima kasih atas partisipasi Anda. Laporan ini telah terenkripsi dan diteruskan ke petugas desa terkait untuk ditindaklanjuti.
          </p>

          <div class="my-6 p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
            <div class="text-left">
              <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Nomor Tiket Pengaduan</span>
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
              class="flex-1 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors shadow-xs"
            >
              Lacak Tindak Lanjut
            </RouterLink>
            <button
              @click="resetForm"
              class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors"
            >
              Tulis Laporan Lain
            </button>
          </div>
        </div>
      </div>

      <!-- Main Form -->
      <form @submit.prevent="handleSubmit" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Kategori Pengaduan <span class="text-rose-500">*</span>
          </label>
          <select
            v-model="form.category"
            required
            class="w-full text-sm bg-white border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all"
          >
            <option value="Infrastruktur & Jalan">Infrastruktur & Jalan Rusak</option>
            <option value="Fasilitas Umum & Kebersihan">Fasilitas Umum & Kebersihan</option>
            <option value="Bantuan Sosial (Bansos)">Bantuan Sosial (Bansos / BLT)</option>
            <option value="Ketertiban & Keamanan">Ketertiban & Keamanan Lingkungan</option>
            <option value="Pelayanan Aparatur Desa">Pelayanan Petugas Aparatur Desa</option>
            <option value="Lainnya">Lainnya / Saran Pembangunan</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Judul Pengaduan <span class="text-rose-500">*</span>
          </label>
          <input
            v-model="form.complaint_title"
            type="text"
            required
            placeholder="Contoh: Penerangan Jalan Mati di RT 04 Dusun 2"
            class="w-full text-sm border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all"
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Isi Rinci Pengaduan / Aspirasi <span class="text-rose-500">*</span>
          </label>
          <textarea
            v-model="form.complaint_content"
            rows="4"
            required
            placeholder="Jelaskan secara jelas kronologi, dampak, dan apa yang diharapkan..."
            class="w-full text-sm border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all"
          ></textarea>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Lokasi Kejadian
          </label>
          <input
            v-model="form.location"
            type="text"
            placeholder="Contoh: Depan Balai Pertemuan RW 03"
            class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all"
          />
        </div>

        <!-- Identitas Pelapor -->
        <div class="pt-4 border-t border-slate-100">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
            Identitas Pelapor (Data Dilindungi & Terenkripsi)
          </label>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Nama Pelapor <span class="text-rose-500">*</span></label>
              <input
                v-model="form.reporter_name"
                type="text"
                required
                placeholder="Nama Anda atau Anonim"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800"
              />
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-600 mb-1">Nomor WhatsApp / HP</label>
              <input
                v-model="form.reporter_phone"
                type="tel"
                placeholder="Untuk konfirmasi tindak lanjut"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800"
              />
            </div>
          </div>
        </div>

        <!-- Upload Foto Bukti -->
        <div class="pt-4 border-t border-slate-100">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Foto Bukti Lampiran (Opsional)
          </label>
          <p class="text-xs text-slate-400 mb-3">Unggah foto kondisi lapangan atau dokumen terkait (JPG, PNG, PDF maks 2MB).</p>

          <input
            type="file"
            accept="image/jpeg,image/png,application/pdf"
            @change="handleEvidenceChange"
            class="text-xs text-slate-500 file:mr-2 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
          />
        </div>

        <div v-if="errorMessage" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
          {{ errorMessage }}
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
          <RouterLink to="/" class="px-5 py-3 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
            Batal
          </RouterLink>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-8 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md disabled:opacity-50 flex items-center gap-2"
          >
            <span>{{ isSubmitting ? 'Mengenkripsi & Mengirim...' : 'Kirim Pengaduan' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { RouterLink } from 'vue-router';
import { publicService } from '../../services/publicService';
import EncryptionShield from '../../components/common/EncryptionShield.vue';

const isSubmitting = ref(false);
const errorMessage = ref('');
const submittedTicket = ref('');
const copied = ref(false);
const evidenceFile = ref(null);

const form = reactive({
  category: 'Infrastruktur & Jalan',
  complaint_title: '',
  complaint_content: '',
  location: '',
  reporter_name: '',
  reporter_phone: '',
});

const handleEvidenceChange = (e) => {
  const file = e.target.files[0];
  if (file && file.size > 2048 * 1024) {
    alert('Ukuran file bukti melebihi 2MB.');
    e.target.value = '';
    return;
  }
  evidenceFile.value = file;
};

const copyTicket = () => {
  navigator.clipboard.writeText(submittedTicket.value);
  copied.value = true;
  setTimeout(() => (copied.value = false), 2000);
};

const resetForm = () => {
  submittedTicket.value = '';
  form.complaint_title = '';
  form.complaint_content = '';
  form.location = '';
  form.reporter_name = '';
  form.reporter_phone = '';
  evidenceFile.value = null;
};

const handleSubmit = async () => {
  errorMessage.value = '';
  isSubmitting.value = true;

  try {
    const files = evidenceFile.value ? [evidenceFile.value] : [];
    const res = await publicService.submitComplaint(form, files);
    if (res.success && res.ticket_number) {
      submittedTicket.value = res.ticket_number;
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      const errList = Object.values(err.response.data.errors).flat();
      errorMessage.value = errList.join(' • ');
    } else {
      errorMessage.value = err.response?.data?.message || 'Gagal mengirim laporan pengaduan.';
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

