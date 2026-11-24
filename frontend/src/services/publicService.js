import api from './api';
import { encryptSensitivePayload } from './crypto';

export const publicService = {
  /**
   * Dapatkan katalog jenis layanan surat online.
   */
  async getServiceTypes() {
    const response = await api.get('/public/service-types');
    return response.data.data;
  },

  /**
   * Submit pengajuan surat warga (dengan Enkripsi In-Transit otomatis).
   */
  async submitService(data, files = [], documentTypes = []) {
    // 1. Siapkan data sensitif untuk dienkripsi di sisi klien
    const sensitiveData = {
      service_type_id: data.service_type_id,
      nik: data.nik,
      kk_number: data.kk_number,
      full_name: data.full_name,
      phone_number: data.phone_number,
      address: data.address,
      purpose_description: data.purpose_description,
    };

    // 2. Enkripsi data sensitif dengan RSA + AES Session Key
    const encryptedPayload = await encryptSensitivePayload(sensitiveData);

    // 3. Buat FormData untuk mengirim data + berkas lampiran privat
    const formData = new FormData();

    if (encryptedPayload._encrypted) {
      formData.append('_encrypted', '1');
      formData.append('encrypted_key', encryptedPayload.encrypted_key);
      formData.append('iv', encryptedPayload.iv);
      formData.append('data', encryptedPayload.data);
      formData.append('tag', encryptedPayload.tag);
      formData.append('mode', encryptedPayload.mode);
    } else {
      // Fallback tanpa enkripsi
      Object.keys(sensitiveData).forEach((key) => {
        if (sensitiveData[key] !== undefined && sensitiveData[key] !== null) {
          formData.append(key, sensitiveData[key]);
        }
      });
    }

    // Lampirkan dokumen file pendukung
    files.forEach((file, index) => {
      formData.append(`documents[${index}]`, file);
      if (documentTypes[index]) {
        formData.append(`document_types[${index}]`, documentTypes[index]);
      }
    });

    const response = await api.post('/public/services', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    return response.data;
  },

  /**
   * Submit pengaduan warga (dengan Enkripsi In-Transit).
   */
  async submitComplaint(data, files = []) {
    const sensitiveData = {
      reporter_name: data.reporter_name,
      reporter_nik: data.reporter_nik,
      reporter_phone: data.reporter_phone,
      complaint_title: data.complaint_title,
      complaint_content: data.complaint_content,
      location: data.location,
      category: data.category,
    };

    const encryptedPayload = await encryptSensitivePayload(sensitiveData);
    const formData = new FormData();

    if (encryptedPayload._encrypted) {
      formData.append('_encrypted', '1');
      formData.append('encrypted_key', encryptedPayload.encrypted_key);
      formData.append('iv', encryptedPayload.iv);
      formData.append('data', encryptedPayload.data);
      formData.append('tag', encryptedPayload.tag);
      formData.append('mode', encryptedPayload.mode);
    } else {
      Object.keys(sensitiveData).forEach((key) => {
        if (sensitiveData[key]) {
          formData.append(key, sensitiveData[key]);
        }
      });
    }

    files.forEach((file, index) => {
      formData.append(`documents[${index}]`, file);
    });

    const response = await api.post('/public/complaints', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    return response.data;
  },

  /**
   * Lacak status tiket pengajuan surat atau pengaduan.
   */
  async trackTicket(ticketNumber) {
    const clean = ticketNumber.trim().toUpperCase();
    const response = await api.get(`/public/track/${encodeURIComponent(clean)}`);
    return response.data.data;
  },

  /**
   * Dapatkan dokumen surat resmi digital yang telah selesai diterbitkan via tiket.
   */
  async getPublicLetter(ticketNumber) {
    const clean = ticketNumber.trim().toUpperCase();
    const response = await api.get(`/public/track/${encodeURIComponent(clean)}/letter`);
    return response.data.data;
  },
};

