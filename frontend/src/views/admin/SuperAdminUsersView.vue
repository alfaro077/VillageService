<template>
  <div class="space-y-6 max-w-5xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200 mb-1">
          <span>👑 Fitur Khusus Superadmin</span>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Akun Petugas & Admin</h2>
        <p class="text-xs text-slate-500 mt-1">Daftarkan staf baru dan atur hak akses petugas pelayanan desa.</p>
      </div>

      <button
        @click="openAddModal"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md shrink-0"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        Tambah Petugas Baru
      </button>
    </div>

    <div v-if="alertMessage" :class="['p-4 rounded-xl text-xs flex items-center justify-between', alertSuccess ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800']">
      <span>{{ alertMessage }}</span>
      <button @click="alertMessage = ''" class="font-bold text-xs">✕</button>
    </div>

    <!-- Table Users -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
      <div v-if="isLoading" class="p-12 text-center text-xs text-slate-400">
        Memuat daftar akun petugas...
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold">
            <tr>
              <th class="px-6 py-3.5">Nama Petugas</th>
              <th class="px-6 py-3.5">Alamat Email</th>
              <th class="px-6 py-3.5">Peran (Role)</th>
              <th class="px-6 py-3.5">Dibuat Pada</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-900">{{ user.name }}</td>
              <td class="px-6 py-4 font-mono text-slate-600">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase',
                    user.role === 'superadmin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-700 border border-slate-200'
                  ]"
                >
                  {{ user.role === 'superadmin' ? '👑 Superadmin' : 'Petugas Admin' }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-400">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="openEditModal(user)"
                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 font-bold text-slate-700 text-[11px]"
                  >
                    Edit
                  </button>
                  <button
                    v-if="user.id !== currentUserId"
                    @click="handleDelete(user)"
                    class="px-2.5 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 font-bold text-rose-700 text-[11px]"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form Tambah / Edit -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <h3 class="text-base font-extrabold text-slate-900">
            {{ isEditing ? 'Edit Akun Staf' : 'Tambah Akun Staf Baru' }}
          </h3>
          <button @click="showModal = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-lg">✕</button>
        </div>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Petugas *</label>
            <input
              v-model="modalForm.name"
              type="text"
              required
              placeholder="Contoh: Budi Prasetyo"
              class="w-full text-xs border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Email Resmi *</label>
            <input
              v-model="modalForm.email"
              type="email"
              required
              placeholder="petugas@desa.id"
              class="w-full text-xs border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Kata Sandi {{ isEditing ? '(Kosongkan jika tidak diubah)' : '*' }}
            </label>
            <input
              v-model="modalForm.password"
              type="password"
              :required="!isEditing"
              minlength="8"
              placeholder="Minimal 8 karakter"
              class="w-full text-xs border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tingkat Hak Akses (Role) *</label>
            <select
              v-model="modalForm.role"
              required
              class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800"
            >
              <option value="admin">Petugas Pelayanan (Admin Operasional)</option>
              <option value="superadmin">Superadmin (Pengelola Layanan & Audit)</option>
            </select>
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
              {{ isSaving ? 'Menyimpan...' : 'Simpan Akun' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { superAdminService } from '../../services/superAdminService';
import { authService } from '../../services/authService';

const isLoading = ref(true);
const isSaving = ref(false);
const users = ref([]);
const alertMessage = ref('');
const alertSuccess = ref(true);

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const currentUserId = computed(() => authService.getUser()?.id);

const modalForm = reactive({
  name: '',
  email: '',
  password: '',
  role: 'admin',
});

const formatDate = (isoStr) => {
  if (!isoStr) return '-';
  const d = new Date(isoStr);
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
};

const loadUsers = async () => {
  isLoading.value = true;
  try {
    users.value = await superAdminService.getUsers();
  } catch (err) {
    alertMessage.value = 'Gagal memuat pengguna.';
    alertSuccess.value = false;
  } finally {
    isLoading.value = false;
  }
};

const openAddModal = () => {
  isEditing.value = false;
  editingId.value = null;
  modalForm.name = '';
  modalForm.email = '';
  modalForm.password = '';
  modalForm.role = 'admin';
  showModal.value = true;
};

const openEditModal = (user) => {
  isEditing.value = true;
  editingId.value = user.id;
  modalForm.name = user.name;
  modalForm.email = user.email;
  modalForm.password = '';
  modalForm.role = user.role;
  showModal.value = true;
};

const saveUser = async () => {
  isSaving.value = true;
  try {
    if (isEditing.value) {
      await superAdminService.updateUser(editingId.value, modalForm);
      alertMessage.value = `Akun staf '${modalForm.name}' berhasil diperbarui.`;
    } else {
      await superAdminService.createUser(modalForm);
      alertMessage.value = `Akun staf '${modalForm.name}' berhasil dibuat.`;
    }
    alertSuccess.value = true;
    showModal.value = false;
    await loadUsers();
  } catch (err) {
    alertMessage.value = err.response?.data?.message || 'Gagal menyimpan akun.';
    alertSuccess.value = false;
  } finally {
    isSaving.value = false;
  }
};

const handleDelete = async (user) => {
  if (confirm(`Yakin ingin menghapus akun staf '${user.name}'?`)) {
    try {
      const res = await superAdminService.deleteUser(user.id);
      alertMessage.value = res.message;
      alertSuccess.value = true;
      await loadUsers();
    } catch (err) {
      alertMessage.value = err.response?.data?.message || 'Gagal menghapus pengguna.';
      alertSuccess.value = false;
    }
  }
};

onMounted(() => {
  loadUsers();
});
</script>

