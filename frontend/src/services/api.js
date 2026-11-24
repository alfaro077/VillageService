import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api/v1',
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// Interceptor untuk menambahkan Bearer Token pada setiap request admin
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('desa_admin_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

// Interceptor respon untuk menangani sesi kedaluwarsa (401)
api.interceptors.response.use((response) => {
  return response;
}, (error) => {
  if (error.response && error.response.status === 401) {
    // Jika request menuju admin dan unauthorized, hapus token dan redirect ke login
    if (window.location.pathname.startsWith('/admin') && !window.location.pathname.includes('/admin/login')) {
      localStorage.removeItem('desa_admin_token');
      localStorage.removeItem('desa_admin_user');
      window.location.href = '/admin/login';
    }
  }
  return Promise.reject(error);
});

export default api;

