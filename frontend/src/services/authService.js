import api from './api';

export const authService = {
  /**
   * Login Admin via Sanctum.
   */
  async login(email, password) {
    const response = await api.post('/auth/login', { email, password });
    if (response.data.success && response.data.token) {
      localStorage.setItem('desa_admin_token', response.data.token);
      localStorage.setItem('desa_admin_user', JSON.stringify(response.data.user));
    }
    return response.data;
  },

  /**
   * Logout dan hapus token lokal.
   */
  async logout() {
    try {
      await api.post('/auth/logout');
    } catch {
      // Abaikan error jika token sudah tidak valid di server
    } finally {
      localStorage.removeItem('desa_admin_token');
      localStorage.removeItem('desa_admin_user');
    }
  },

  /**
   * Cek status autentikasi admin.
   */
  isAuthenticated() {
    return !!localStorage.getItem('desa_admin_token');
  },

  /**
   * Cek apakah user memiliki peran superadmin.
   */
  isSuperAdmin() {
    const user = this.getUser();
    return user?.role === 'superadmin';
  },

  /**
   * Ambil data profil admin yang sedang login.
   */
  getUser() {
    const raw = localStorage.getItem('desa_admin_user');
    return raw ? JSON.parse(raw) : null;
  },

  /**
   * Ambil bearer token saat ini.
   */
  getToken() {
    return localStorage.getItem('desa_admin_token');
  },
};

