<template>
  <div class="min-h-screen flex flex-col bg-white">
    <!-- Navbar Header Bersih & Modern -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 transition-all">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
          <!-- Logo & Brand -->
          <RouterLink to="/" class="flex items-center gap-3.5 group">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-sm shadow-emerald-200 group-hover:scale-105 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <span class="block text-lg font-bold tracking-tight text-slate-900 group-hover:text-emerald-600 transition-colors">
                Desa Mandiri Sejahtera
              </span>
              <span class="block text-xs font-medium text-slate-500">
                Portal Pelayanan Administrasi Digital
              </span>
            </div>
          </RouterLink>

          <!-- Desktop Navigation -->
          <nav class="hidden md:flex items-center gap-1">
            <RouterLink
              to="/"
              class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.name === 'home' ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
            >
              Beranda
            </RouterLink>

            <RouterLink
              to="/layanan"
              class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.name === 'layanan' ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
            >
              Pengajuan Surat
            </RouterLink>

            <RouterLink
              to="/pengaduan"
              class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.name === 'pengaduan' ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
            >
              Pengaduan Warga
            </RouterLink>

            <RouterLink
              to="/lacak"
              class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors"
              :class="$route.name === 'lacak' ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
            >
              Lacak Status
            </RouterLink>
          </nav>

          <!-- Action Button: Portal Admin -->
          <div class="hidden md:flex items-center gap-3">
            <RouterLink
              to="/admin/login"
              class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all shadow-xs"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Masuk Petugas
            </RouterLink>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100"
            aria-label="Toggle menu"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile Nav Menu -->
        <div v-show="mobileMenuOpen" class="md:hidden border-t border-slate-100 py-3 space-y-1">
          <RouterLink
            @click="mobileMenuOpen = false"
            to="/"
            class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:bg-slate-50"
          >
            Beranda
          </RouterLink>
          <RouterLink
            @click="mobileMenuOpen = false"
            to="/layanan"
            class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:bg-slate-50"
          >
            Pengajuan Surat
          </RouterLink>
          <RouterLink
            @click="mobileMenuOpen = false"
            to="/pengaduan"
            class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:bg-slate-50"
          >
            Pengaduan Warga
          </RouterLink>
          <RouterLink
            @click="mobileMenuOpen = false"
            to="/lacak"
            class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 hover:bg-slate-50"
          >
            Lacak Status
          </RouterLink>
          <div class="pt-2 border-t border-slate-100">
            <RouterLink
              @click="mobileMenuOpen = false"
              to="/admin/login"
              class="block w-full text-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-slate-900 text-white"
            >
              Login Petugas Desa
            </RouterLink>
          </div>
        </div>
      </div>
    </header>

    <!-- Content Slot -->
    <main class="flex-1">
      <RouterView />
    </main>

    <!-- Footer Publik Bersih & Formal -->
    <footer class="bg-white border-t border-slate-200 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Kolom 1: Profil Singkat -->
          <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold">
                🏛️
              </div>
              <span class="text-base font-bold text-slate-900">Pemerintah Desa Mandiri Sejahtera</span>
            </div>
            <p class="text-sm text-slate-500 leading-relaxed max-w-md">
              Mewujudkan tata kelola desa yang transparan, akuntabel, dan mengutamakan kemudahan akses layanan kependudukan secara digital dan aman bagi seluruh masyarakat.
            </p>
            <div class="mt-4 flex items-center gap-2 text-xs text-slate-600 font-medium">
              <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
              Sistem Aktif 24 Jam • Pelayanan Jam Kerja: 08.00 - 15.30 WIB
            </div>
          </div>

          <!-- Kolom 2: Tautan Cepat -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-3">Layanan Populer</h4>
            <ul class="space-y-2 text-sm text-slate-600">
              <li><RouterLink to="/layanan" class="hover:text-emerald-600">Surat Keterangan Domisili</RouterLink></li>
              <li><RouterLink to="/layanan" class="hover:text-emerald-600">Surat Keterangan Usaha (SKU)</RouterLink></li>
              <li><RouterLink to="/layanan" class="hover:text-emerald-600">Surat Keterangan Tidak Mampu</RouterLink></li>
              <li><RouterLink to="/pengaduan" class="hover:text-emerald-600">Pengaduan Fasilitas Publik</RouterLink></li>
            </ul>
          </div>

          <!-- Kolom 3: Kontak & Keamanan -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-3">Pusat Bantuan</h4>
            <p class="text-sm text-slate-600 mb-1">Kantor Balai Desa Mandiri</p>
            <p class="text-sm text-slate-500">Jl. Raya Protokol Desa No. 01</p>
            <p class="text-sm text-emerald-700 font-semibold mt-2">WhatsApp: 0812-3456-7890</p>
            <div class="mt-3 text-xs text-slate-600 flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              Dilindungi AES-256 Bit
            </div>
          </div>
        </div>

        <div class="border-t border-slate-100 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
          <p>© 2026 Pemerintah Desa Mandiri Sejahtera. Seluruh Hak Cipta Dilindungi.</p>
          <div class="flex items-center gap-4">
            <span>Privasi & Keamanan Warga</span>
            <span>•</span>
            <RouterLink to="/admin/login" class="hover:text-slate-800">Login Administrator</RouterLink>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink, RouterView } from 'vue-router';

const mobileMenuOpen = ref(false);
</script>

