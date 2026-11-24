import api from './api';

export const superAdminService = {
  // 1. Manajemen Master Jenis Layanan Surat
  async getServiceTypes() {
    const response = await api.get('/superadmin/service-types');
    return response.data.data;
  },

  async createServiceType(payload) {
    const response = await api.post('/superadmin/service-types', payload);
    return response.data;
  },

  async updateServiceType(id, payload) {
    const response = await api.put(`/superadmin/service-types/${id}`, payload);
    return response.data;
  },

  async toggleServiceType(id) {
    const response = await api.patch(`/superadmin/service-types/${id}/toggle`);
    return response.data;
  },

  async deleteServiceType(id) {
    const response = await api.delete(`/superadmin/service-types/${id}`);
    return response.data;
  },

  // 2. Monitoring & Log Aktivitas Sistem
  async getActivityLogs(params = {}) {
    const response = await api.get('/superadmin/activity-logs', { params });
    return response.data;
  },

  async getActivityStats() {
    const response = await api.get('/superadmin/activity-logs/stats');
    return response.data;
  },

  // 3. Manajemen Staf Pengguna Admin
  async getUsers() {
    const response = await api.get('/superadmin/users');
    return response.data.data;
  },

  async createUser(payload) {
    const response = await api.post('/superadmin/users', payload);
    return response.data;
  },

  async updateUser(id, payload) {
    const response = await api.put(`/superadmin/users/${id}`, payload);
    return response.data;
  },

  async deleteUser(id) {
    const response = await api.delete(`/superadmin/users/${id}`);
    return response.data;
  },
};

