import { createRouter, createWebHistory } from 'vue-router';
import { authService } from '../services/authService';

// Layouts
import PublicLayout from '../layouts/PublicLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';

// Public Views
import HomeView from '../views/public/HomeView.vue';
import FormLayananView from '../views/public/FormLayananView.vue';
import FormPengaduanView from '../views/public/FormPengaduanView.vue';
import TrackingView from '../views/public/TrackingView.vue';

// Admin Views
import LoginView from '../views/admin/LoginView.vue';
import DashboardView from '../views/admin/DashboardView.vue';
import AdminServicesView from '../views/admin/AdminServicesView.vue';
import AdminServiceDetailView from '../views/admin/AdminServiceDetailView.vue';
import AdminComplaintsView from '../views/admin/AdminComplaintsView.vue';
import AdminComplaintDetailView from '../views/admin/AdminComplaintDetailView.vue';

// SuperAdmin Views
import SuperAdminServiceTypesView from '../views/admin/SuperAdminServiceTypesView.vue';
import SuperAdminActivityLogsView from '../views/admin/SuperAdminActivityLogsView.vue';
import SuperAdminUsersView from '../views/admin/SuperAdminUsersView.vue';

const routes = [
  // 1. Rute Publik (Warga)
  {
    path: '/',
    component: PublicLayout,
    children: [
      {
        path: '',
        name: 'home',
        component: HomeView,
        meta: { title: 'Beranda & Profil Desa' },
      },
      {
        path: 'layanan',
        name: 'layanan',
        component: FormLayananView,
        meta: { title: 'Portal Pengajuan Surat Online' },
      },
      {
        path: 'pengaduan',
        name: 'pengaduan',
        component: FormPengaduanView,
        meta: { title: 'Layanan Pengaduan & Aspirasi Warga' },
      },
      {
        path: 'lacak/:ticket?',
        name: 'lacak',
        component: TrackingView,
        meta: { title: 'Lacak Status Pengajuan' },
      },
    ],
  },

  // 2. Rute Login Admin (Guest Only)
  {
    path: '/admin/login',
    name: 'admin-login',
    component: LoginView,
    meta: { title: 'Login Admin Desa', guestOnly: true },
  },

  // 3. Rute Panel Admin (Terproteksi Sanctum)
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard',
      },
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: DashboardView,
        meta: { title: 'Dashboard Ringkasan Admin' },
      },
      {
        path: 'layanan',
        name: 'admin-services',
        component: AdminServicesView,
        meta: { title: 'Manajemen Layanan Surat' },
      },
      {
        path: 'layanan/:id',
        name: 'admin-service-detail',
        component: AdminServiceDetailView,
        meta: { title: 'Detail Pengajuan Warga' },
      },
      {
        path: 'pengaduan',
        name: 'admin-complaints',
        component: AdminComplaintsView,
        meta: { title: 'Manajemen Pengaduan Warga' },
      },
      {
        path: 'pengaduan/:id',
        name: 'admin-complaint-detail',
        component: AdminComplaintDetailView,
        meta: { title: 'Detail Pengaduan Warga' },
      },
      // SuperAdmin Exclusives
      {
        path: 'master-layanan',
        name: 'superadmin-services',
        component: SuperAdminServiceTypesView,
        meta: { title: 'Kelola Master Layanan Surat', requiresSuperAdmin: true },
      },
      {
        path: 'log-aktivitas',
        name: 'superadmin-logs',
        component: SuperAdminActivityLogsView,
        meta: { title: 'Monitoring Log Aktivitas Sistem', requiresSuperAdmin: true },
      },
      {
        path: 'kelola-petugas',
        name: 'superadmin-users',
        component: SuperAdminUsersView,
        meta: { title: 'Manajemen Akun Petugas Desa', requiresSuperAdmin: true },
      },
    ],
  },

  // 4. Fallback 404
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

// Navigation Guard untuk otentikasi admin & superadmin
router.beforeEach((to, from, next) => {
  const isAuth = authService.isAuthenticated();
  const isSuper = authService.isSuperAdmin();

  // Set page title
  document.title = to.meta.title
    ? `${to.meta.title} - Desa Mandiri`
    : 'Portal Pelayanan Desa Online';

  if (to.meta.requiresAuth && !isAuth) {
    next({ name: 'admin-login' });
  } else if (to.meta.guestOnly && isAuth) {
    next({ name: 'admin-dashboard' });
  } else if (to.meta.requiresSuperAdmin && !isSuper) {
    next({ name: 'admin-dashboard' });
  } else {
    next();
  }
});

export default router;

