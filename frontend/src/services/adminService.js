import api from './api';

export const adminService = {
  /**
   * Dapatkan statistik dashboard admin.
   */
  async getDashboardStats() {
    const response = await api.get('/admin/dashboard');
    return response.data;
  },

  /**
   * Dapatkan daftar pengajuan surat dengan paginasi dan filter.
   */
  async getServices(params = {}) {
    const response = await api.get('/admin/services', { params });
    return response.data;
  },

  /**
   * Dapatkan detail lengkap pengajuan surat yang telah didekripsi khusus Admin.
   */
  async getServiceDetail(id) {
    const response = await api.get(`/admin/services/${id}`);
    return response.data.data;
  },

  /**
   * Update status pengajuan dan catatan petugas.
   */
  async updateServiceStatus(id, status, notes = '') {
    const response = await api.patch(`/admin/services/${id}/status`, {
      status,
      admin_notes: notes,
    });
    return response.data;
  },

  /**
   * Dapatkan dokumen surat resmi dan notifikasi WhatsApp untuk pengajuan surat.
   */
  async getServiceLetter(id) {
    const response = await api.get(`/admin/services/${id}/letter`);
    return response.data.data;
  },

  /**
   * Unduh file PDF dokumen resmi pengajuan layanan (Blob download).
   */
  async downloadServicePdf(id, filename = 'surat_resmi.pdf') {
    const response = await api.get(`/admin/services/${id}/pdf`, {
      responseType: 'blob',
    });
    const blob = new Blob([response.data], { type: 'application/pdf' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(() => window.URL.revokeObjectURL(url), 1000);
    return blob;
  },

  /**
   * Kirim dokumen surat resmi via WhatsApp Gateway atau dapatkan link WhatsApp.
   */
  async sendServiceWhatsApp(id) {
    const response = await api.post(`/admin/services/${id}/send-whatsapp`);
    return response.data;
  },

  /**
   * Dapatkan daftar laporan pengaduan warga.
   */
  async getComplaints(params = {}) {
    const response = await api.get('/admin/complaints', { params });
    return response.data;
  },

  /**
   * Dapatkan detail pengaduan terdekripsi.
   */
  async getComplaintDetail(id) {
    const response = await api.get(`/admin/complaints/${id}`);
    return response.data.data;
  },

  /**
   * Update status dan tanggapan admin pengaduan warga.
   */
  async updateComplaintStatus(id, status, responseText = '') {
    const response = await api.patch(`/admin/complaints/${id}/status`, {
      status,
      admin_response: responseText,
    });
    return response.data;
  },

  /**
   * Unduh dokumen privat dengan token Bearer.
   */
  async downloadDocument(docId, fileName) {
    const response = await api.get(`/admin/documents/${docId}/download`, {
      responseType: 'blob',
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', fileName || 'dokumen');
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  },

  /**
   * Buka preview dokumen privat di tab baru menggunakan blob url aman.
   */
  async previewDocument(docId) {
    const response = await api.get(`/admin/documents/${docId}/view`, {
      responseType: 'blob',
    });
    const blob = new Blob([response.data], { type: response.headers['content-type'] });
    const url = window.URL.createObjectURL(blob);
    window.open(url, '_blank');
  },
};

