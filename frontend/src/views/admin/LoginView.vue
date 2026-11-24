<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
      <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center mx-auto mb-4 shadow-md font-bold text-lg">
        🏛️
      </div>
      <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Login Portal Petugas Desa</h2>
      <p class="text-xs text-slate-500 mt-1.5">
        Akses khusus petugas dan administrator pelayanan desa terpadu.
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
      <div class="bg-white py-8 px-6 sm:px-10 rounded-3xl shadow-sm border border-slate-200">
        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Email Administrator
            </label>
            <input
              v-model="email"
              type="email"
              required
              placeholder="admin@desa.id"
              class="w-full text-sm border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all"
            />
          </div>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                Kata Sandi
              </label>
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 transition-colors"
              >
                {{ showPassword ? 'Sembunyikan' : 'Tampilkan' }}
              </button>
            </div>
            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="••••••••"
                class="w-full text-sm border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all font-mono"
              />
            </div>
          </div>

          <div v-if="errorMessage" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            {{ errorMessage }}
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
          >
            <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isLoading ? 'Memverifikasi...' : 'Masuk ke Dashboard' }}</span>
          </button>
        </form>

        <!-- Kredensial Pengujian Default & Quick Fill -->
        <div class="mt-6 pt-6 border-t border-slate-100">
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">
            Pilih Cepat Akun Pengujian:
          </span>
          <div class="grid grid-cols-2 gap-2">
            <button
              type="button"
              @click="setAccount('superadmin@desa.id', 'SuperAdmin123!')"
              class="p-2.5 rounded-xl border text-left transition-all cursor-pointer"
              :class="email === 'superadmin@desa.id' ? 'border-blue-600 bg-blue-50/50 text-blue-900 ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-300 bg-slate-50 text-slate-700'"
            >
              <div class="flex items-center gap-1 font-bold text-xs mb-0.5">
                <span>👑 Superadmin</span>
              </div>
              <p class="text-[10px] text-slate-500 font-mono truncate">superadmin@desa.id</p>
              <p class="text-[9px] text-slate-400">Full Access (Master + Audit)</p>
            </button>

            <button
              type="button"
              @click="setAccount('admin@desa.id', 'Admin123!')"
              class="p-2.5 rounded-xl border text-left transition-all cursor-pointer"
              :class="email === 'admin@desa.id' ? 'border-slate-800 bg-slate-100 text-slate-900 ring-1 ring-slate-800' : 'border-slate-200 hover:border-slate-300 bg-slate-50 text-slate-700'"
            >
              <div class="flex items-center gap-1 font-bold text-xs mb-0.5">
                <span>🛡️ Staf Admin</span>
              </div>
              <p class="text-[10px] text-slate-500 font-mono truncate">admin@desa.id</p>
              <p class="text-[9px] text-slate-400">Operasional Layanan</p>
            </button>
          </div>
        </div>

        <div class="mt-6 text-center">
          <RouterLink to="/" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            ← Kembali ke Portal Publik
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { authService } from '../../services/authService';

const router = useRouter();
const email = ref('superadmin@desa.id');
const password = ref('SuperAdmin123!');
const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

const setAccount = (targetEmail, targetPassword) => {
  email.value = targetEmail;
  password.value = targetPassword;
  errorMessage.value = '';
};

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const res = await authService.login(email.value, password.value);
    if (res.success) {
      router.push('/admin/dashboard');
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Login gagal. Periksa kembali email dan password Anda.';
  } finally {
    isLoading.value = false;
  }
};
</script>

