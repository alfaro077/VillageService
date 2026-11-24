<template>
  <div class="min-h-screen bg-slate-50 flex">
    <!-- Sidebar Admin Modern -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0">
      <!-- Brand Sidebar -->
      <div class="h-20 px-6 border-b border-slate-100 flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-xs"
          :class="isSuperAdmin ? 'bg-blue-600' : 'bg-slate-900'"
        >
          {{ isSuperAdmin ? 'SUP' : 'ADM' }}
        </div>
        <div>
          <h2 class="text-sm font-bold text-slate-900 tracking-tight">
            {{ isSuperAdmin ? 'Superadmin Desa' : 'Admin Desa' }}
          </h2>
          <span class="text-xs text-slate-400 font-medium">Panel Layanan Online</span>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
        <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
          Operasional
        </div>

        <RouterLink
          to="/admin/dashboard"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
          :class="$route.name === 'admin-dashboard' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Dashboard
        </RouterLink>

        <RouterLink
          to="/admin/layanan"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
          :class="$route.name?.startsWith('admin-service') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Permohonan Surat
        </RouterLink>

        <RouterLink
          to="/admin/pengaduan"
          class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
          :class="$route.name?.startsWith('admin-complaint') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
          </svg>
          Pengaduan Warga
        </RouterLink>

        <!-- Menu Khusus Superadmin -->
        <template v-if="isSuperAdmin">
          <div class="pt-5 mt-5 border-t border-slate-100">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-blue-600 flex items-center justify-between">
              <span>Superadmin Kontrol</span>
              <span class="px-1.5 py-0.5 rounded text-[9px] bg-blue-50 text-blue-700 border border-blue-100">Root</span>
            </div>

            <RouterLink
              to="/admin/master-layanan"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
              :class="$route.name === 'superadmin-services' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Master Layanan
            </RouterLink>

            <RouterLink
              to="/admin/log-aktivitas"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
              :class="$route.name === 'superadmin-logs' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              Monitoring Log Audit
            </RouterLink>

            <RouterLink
              to="/admin/kelola-petugas"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
              :class="$route.name === 'superadmin-users' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Kelola Petugas
            </RouterLink>
          </div>
        </template>

        <div class="pt-6 mt-6 border-t border-slate-100">
          <RouterLink
            to="/"
            target="_blank"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            Buka Portal Publik
          </RouterLink>
        </div>
      </nav>

      <!-- User Profile & Logout Bottom -->
      <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <div class="flex items-center justify-between">
          <div class="min-w-0">
            <div class="flex items-center gap-1.5">
              <p class="text-xs font-bold text-slate-900 truncate">{{ adminUser?.name || 'Administrator' }}</p>
              <span
                v-if="isSuperAdmin"
                class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-100 text-blue-700 uppercase"
              >
                Super
              </span>
            </div>
            <p class="text-[11px] text-slate-400 truncate">{{ adminUser?.email || 'admin@desa.id' }}</p>
          </div>
          <button
            @click="handleLogout"
            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
            title="Keluar"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
      <!-- Top Bar -->
      <header class="h-20 bg-white border-b border-slate-200 px-8 flex items-center justify-between shrink-0">
        <div>
          <h1 class="text-base font-bold text-slate-900">
            {{ $route.meta.title || 'Panel Administrator' }}
          </h1>
          <p class="text-xs text-slate-500">
            {{ isSuperAdmin ? 'Panel Kendali Tingkat Tinggi & Pengawasan Pelayanan Desa' : 'Sistem Pelayanan Administrasi Desa Terdekripsi Aman' }}
          </p>
        </div>

        <div class="flex items-center gap-3">
          <span
            v-if="isSuperAdmin"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200"
          >
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            👑 Superadmin (Root)
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-300"
          >
            <span class="w-2 h-2 rounded-full bg-slate-700"></span>
            🛡️ Sesi Petugas Terverifikasi
          </span>
        </div>
      </header>

      <!-- Main Slot -->
      <main class="flex-1 p-8">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter, RouterLink, RouterView } from 'vue-router';
import { authService } from '../services/authService';

const router = useRouter();
const adminUser = computed(() => authService.getUser());
const isSuperAdmin = computed(() => authService.isSuperAdmin());

const handleLogout = async () => {
  if (confirm('Apakah Anda yakin ingin keluar dari sesi admin?')) {
    await authService.logout();
    router.push('/admin/login');
  }
};
</script>

