<template>
  <div class="space-y-6 max-w-5xl">
    <!-- Breadcrumb & Back -->
    <div>
      <RouterLink to="/admin/layanan" class="text-xs font-semibold text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
        ← Kembali ke Daftar Permohonan
      </RouterLink>
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Detail Pengajuan Surat</span>
          <h2 class="text-2xl font-extrabold text-slate-900 font-mono">{{ detail?.ticket_number || 'Loading...' }}</h2>
        </div>
        <div v-if="detail">
          <StatusBadge :status="detail.status" />
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="h-64 bg-white rounded-2xl border border-slate-200 animate-pulse"></div>

    <div v-else-if="detail" class="space-y-6">
      <!-- Banner Keamanan Dekripsi Admin -->
      <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center gap-3 text-slate-900 text-xs">
        <span class="text-base">🔓</span>
        <div>
          <span class="font-bold block">Data Warga Didekripsi Khusus Sesi Administrator Terverifikasi</span>
          <span class="text-slate-600 text-[11px]">Kunci privat server telah mendekripsi data NIK, No KK, dan kontak untuk keperluan verifikasi berkas pelayanan.</span>
        </div>
      </div>

      <!-- Card Surat Resmi Diterbitkan & Kirim WhatsApp -->
      <div v-if="detail.status === 'selesai' || detail.letter_number" class="bg-gradient-to-r from-slate-900 via-slate-900 to-blue-950 text-white rounded-2xl shadow-md p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 border border-slate-800">
        <div class="space-y-1.5 text-center md:text-left">
          <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            Surat Resmi Selesai Diterbitkan
          </div>
          <h3 class="text-lg font-bold font-mono tracking-wide text-white">
            No. Surat: {{ detail.letter_number || '470/.../DMS/2026' }}
          </h3>
          <p class="text-xs text-slate-300 max-w-lg">
            Dokumen resmi digital dengan kop surat desa, stempel resmi, dan QR code verifikasi sah siap dicetak dan dikirimkan langsung ke nomor WhatsApp warga.
          </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
          <button
            @click="openLetterModal"
            class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer border border-slate-700"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span>Preview Surat</span>
          </button>

          <button
            @click="handleDirectDownloadPdf"
            :disabled="isDownloadingPdf"
            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer disabled:opacity-50"
            title="Unduh berkas PDF resmi desa"
          >
            <svg v-if="isDownloadingPdf" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Unduh File PDF</span>
          </button>

          <button
            v-if="detail.whatsapp?.whatsapp_url"
            @click="handleSendPdfToWhatsApp"
            :disabled="isSendingWhatsapp"
            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer disabled:opacity-50"
            title="Otomatis unduh berkas PDF dan buka WhatsApp pemohon"
          >
            <svg v-if="isSendingWhatsapp" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span>Kirim File PDF ke WhatsApp</span>
          </button>
        </div>
      </div>

      <!-- Card Identitas Warga (Decrypted PII) -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100 flex items-center justify-between">
          <span>Identitas Pemohon</span>
          <span class="text-xs font-normal text-slate-400 capitalize">{{ detail.service_type?.name }}</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
          <div>
            <span class="text-slate-400 block mb-1">Nama Lengkap (Sesuai KTP):</span>
            <span class="text-sm font-bold text-slate-900">{{ detail.full_name }}</span>
          </div>

          <div>
            <span class="text-slate-400 block mb-1">Nomor Induk Kependudukan (NIK):</span>
            <span class="text-sm font-bold text-slate-900 font-mono tracking-wider">{{ detail.nik }}</span>
          </div>

          <div>
            <span class="text-slate-400 block mb-1">Nomor Kartu Keluarga (No KK):</span>
            <span class="text-sm font-bold text-slate-900 font-mono tracking-wider">{{ detail.kk_number }}</span>
          </div>

          <div>
            <span class="text-slate-400 block mb-1">Nomor WhatsApp / HP:</span>
            <div class="flex items-center gap-2">
              <span class="text-sm font-bold text-slate-900 font-mono">{{ detail.phone_number }}</span>
              <a
                :href="detail.whatsapp?.whatsapp_url || `https://wa.me/${cleanPhone(detail.phone_number)}`"
                target="_blank"
                class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-[11px] inline-flex items-center gap-1 shadow-xs transition-colors"
              >
                <span>Chat WhatsApp</span>
                <span>↗</span>
              </a>
            </div>
          </div>

          <div class="sm:col-span-2">
            <span class="text-slate-400 block mb-1">Alamat Lengkap di Desa:</span>
            <p class="text-sm font-medium text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100">
              {{ detail.address }}
            </p>
          </div>

          <div class="sm:col-span-2">
            <span class="text-slate-400 block mb-1">Keperluan / Keterangan Pengajuan:</span>
            <p class="text-sm font-medium text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-100">
              {{ detail.purpose_description || 'Tidak ada keterangan tambahan.' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Card Berkas Lampiran Privat -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-3 border-b border-slate-100">
          Berkas Lampiran Privat (KTP/KK)
        </h3>

        <div v-if="detail.documents.length === 0" class="text-xs text-slate-400">
          Tidak ada berkas lampiran yang diunggah.
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-for="doc in detail.documents"
            :key="doc.id"
            class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between"
          >
            <div class="min-w-0 pr-3">
              <span class="text-xs font-bold text-slate-900 block truncate uppercase">{{ doc.document_type }}</span>
              <span class="text-[11px] text-slate-500 block truncate">{{ doc.original_name }} ({{ formatBytes(doc.file_size) }})</span>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewDoc(doc.id)"
                class="px-2.5 py-1.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg transition-colors shadow-xs"
                title="Buka Preview"
              >
                Lihat
              </button>
              <button
                @click="downloadDoc(doc.id, doc.original_name)"
                class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-colors shadow-xs"
                title="Unduh Berkas"
              >
                Unduh
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Update Status & Catatan Petugas -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
          <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">
            Tindak Lanjut & Pembaruan Status
          </h3>
          <span v-if="detail.status === 'selesai'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Surat Resmi Selesai & Sah
          </span>
        </div>

        <!-- Tombol Aksi Cepat Setujui (Jika belum selesai) -->
        <div v-if="detail.status !== 'selesai'" class="p-4 rounded-2xl bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm border border-emerald-700">
          <div class="space-y-1 text-center sm:text-left">
            <div class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-200">
              <span>⚡</span> Aksi Cepat Persetujuan
            </div>
            <h4 class="text-sm font-bold text-white">Setujui & Selesaikan Permohonan Warga</h4>
            <p class="text-xs text-emerald-100 max-w-md">
              Satu klik untuk menyetujui, menerbitkan nomor surat resmi, mengunduh file PDF, dan otomatis membuka pesan WhatsApp ke nomor <strong class="font-mono text-white">{{ detail.phone_number }}</strong>.
            </p>
          </div>

          <button
            @click="handleQuickApprove"
            :disabled="isSaving"
            class="px-5 py-3 rounded-xl bg-white hover:bg-emerald-50 text-emerald-900 text-xs font-extrabold transition-all shadow-md flex items-center gap-2 shrink-0 cursor-pointer disabled:opacity-50"
          >
            <svg v-if="isSaving" class="animate-spin w-4 h-4 text-emerald-800" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-4 h-4 fill-emerald-700" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span>Setujui & Kirim ke WhatsApp</span>
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Pilih Status Pengajuan:</label>
            <select
              v-model="statusUpdate"
              class="w-full text-sm bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:ring-2 focus:ring-slate-900"
            >
              <option value="pending">Menunggu (Pending)</option>
              <option value="diproses">Sedang Diproses (Verifikasi / Pembuatan)</option>
              <option value="selesai">Selesai (Disetujui & Terbitkan Surat)</option>
              <option value="ditolak">Ditolak (Berkas Tidak Sesuai)</option>
            </select>
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-700 mb-1">
              Catatan untuk Warga (Akan tampil saat warga melacak nomor tiket dan pada pesan WhatsApp):
            </label>
            <textarea
              v-model="notesUpdate"
              rows="3"
              placeholder="Contoh: Berkas telah diverifikasi lengkap dan surat telah disetujui Kepala Desa."
              class="w-full text-sm border border-slate-300 rounded-xl px-4 py-2.5 text-slate-800 focus:ring-2 focus:ring-slate-900"
            ></textarea>
          </div>
        </div>

        <!-- Opsi Otomatis Kirim WhatsApp saat Status Selesai -->
        <div v-if="statusUpdate === 'selesai'" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs space-y-2 animate-in fade-in duration-200">
          <div class="flex items-center gap-2.5">
            <input
              type="checkbox"
              id="auto_send_wa"
              v-model="autoSendWhatsApp"
              class="w-4 h-4 text-emerald-600 rounded-md border-emerald-300 focus:ring-emerald-500 cursor-pointer"
            />
            <label for="auto_send_wa" class="font-bold text-emerald-900 cursor-pointer text-xs">
              Otomatis buka WhatsApp & kirimkan berkas PDF resmi ke pemohon saat disimpan
            </label>
          </div>
          <p class="text-[11px] text-emerald-800 pl-6 leading-relaxed">
            Sistem akan otomatis men-download file PDF resmi dan langsung mengarahkan Anda ke obrolan WhatsApp pemohon (nomor: <strong class="font-mono text-emerald-950">{{ detail.phone_number }}</strong>) dengan tautan berkas PDF dan rincian lengkap surat yang telah diterbitkan.
          </p>
        </div>

        <div v-if="saveMessage" class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl flex items-center justify-between">
          <span>{{ saveMessage }}</span>
          <button @click="saveMessage = ''" class="text-emerald-700 font-bold ml-2">✕</button>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button
            @click="() => handleSaveStatus()"
            :disabled="isSaving"
            class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md disabled:opacity-50 flex items-center gap-2 cursor-pointer"
          >
            <svg v-if="isSaving" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isSaving ? 'Menyimpan & Memproses...' : 'Simpan Pembaruan Status' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Cetak Surat Resmi Desa & WhatsApp -->
    <OfficialLetterModal
      :isOpen="isLetterModalOpen"
      :letterData="letterData"
      :whatsappUrl="detail?.whatsapp?.whatsapp_url"
      :serviceId="detail?.id"
      :ticketNumber="detail?.ticket_number"
      @close="isLetterModalOpen = false"
    />

    <!-- Modal Konfirmasi Notifikasi WhatsApp Terkirim Otomatis & Panduan Lokal -->
    <div v-if="isWaNotificationModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden p-6 sm:p-8 space-y-5 animate-in fade-in zoom-in-95 duration-200 text-center">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl shadow-xs">
          📄
        </div>

        <div class="space-y-1.5">
          <h3 class="text-lg font-bold text-slate-900">
            {{ gatewayDeliveryResult?.sent_via_gateway ? 'File PDF Resmi Telah Terkirim ke WhatsApp!' : 'Surat Selesai & Berkas PDF Siap Dikirim!' }}
          </h3>
          <p class="text-xs text-slate-600 leading-relaxed">
            <template v-if="gatewayDeliveryResult?.sent_via_gateway">
              Berkas dokumen PDF resmi telah berhasil dikirimkan secara otomatis dari server ke nomor WhatsApp pemohon.
            </template>
            <template v-else>
              Status permohonan telah selesai disetujui. Berkas PDF resmi telah otomatis diunduh dan ruang obrolan WhatsApp pemohon telah terbuka.
            </template>
          </p>
        </div>

        <!-- Rincian Pengiriman -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-left space-y-2.5 text-xs">
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Nomor WhatsApp Warga:</span>
            <span class="font-bold font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ lastDispatchedPhone }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Nama Dokumen PDF:</span>
            <span class="font-bold text-slate-900 font-mono text-[11px] truncate max-w-[220px]" :title="lastDownloadedPdfName">
              {{ lastDownloadedPdfName }}
            </span>
          </div>

          <!-- Petunjuk Khusus Pengiriman File di Lingkungan Lokal -->
          <div v-if="!gatewayDeliveryResult?.sent_via_gateway" class="pt-2 border-t border-slate-200 text-[11px] text-slate-700 space-y-1.5 bg-blue-50/60 p-3 rounded-xl border border-blue-200/60">
            <div class="flex items-center gap-1.5 font-bold text-blue-900">
              <span>💡</span>
              <span>Panduan Mengirimkan File di Lingkungan Lokal:</span>
            </div>
            <p class="text-slate-600 leading-relaxed">
              Karena aplikasi berjalan di server lokal, agar warga langsung menerima dokumen fisik di HP mereka:
            </p>
            <ol class="list-decimal pl-4 space-y-1 text-slate-700">
              <li>Berkas PDF di atas telah <strong>otomatis terunduh</strong> ke folder komputer Anda.</li>
              <li>Pada tab WhatsApp Web pemohon yang baru terbuka, <strong>seret (drag & drop)</strong> berkas PDF dari download bar peramban, atau klik ikon klip <strong>📎 Dokumen</strong>.</li>
              <li>Tekan <strong>Kirim</strong>. Warga langsung dapat membaca surat resmi PDF di HP tanpa kendala jaringan lokal!</li>
            </ol>
          </div>
        </div>

        <div class="space-y-2 pt-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <a
              v-if="lastDispatchedWaUrl"
              :href="lastDispatchedWaUrl"
              target="_blank"
              class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
              </svg>
              <span>Buka WhatsApp Lagi</span>
            </a>

            <button
              @click="handleDirectDownloadPdf"
              class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer"
            >
              <span>📥 Unduh Ulang PDF</span>
            </button>
          </div>

          <button
            @click="isWaNotificationModalOpen = false"
            class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer"
          >
            Selesai / Tutup Dialog
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { adminService } from '../../services/adminService';
import StatusBadge from '../../components/common/StatusBadge.vue';
import OfficialLetterModal from '../../components/common/OfficialLetterModal.vue';

const route = useRoute();
const isLoading = ref(true);
const isSaving = ref(false);
const detail = ref(null);
const statusUpdate = ref('pending');
const notesUpdate = ref('');
const saveMessage = ref('');

const isLetterModalOpen = ref(false);
const letterData = ref(null);
const isLetterLoading = ref(false);
const isDownloadingPdf = ref(false);
const isSendingWhatsapp = ref(false);

const autoSendWhatsApp = ref(true);
const isWaNotificationModalOpen = ref(false);
const lastDispatchedWaUrl = ref('');
const lastDispatchedPhone = ref('');
const lastDownloadedPdfName = ref('');
const gatewayDeliveryResult = ref(null);

const cleanPhone = (phone) => {
  if (!phone) return '';
  let p = phone.replace(/[^0-9]/g, '');
  if (p.startsWith('0')) p = '62' + p.substring(1);
  return p;
};

const formatBytes = (bytes) => {
  if (!bytes) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
};

const loadDetail = async () => {
  isLoading.value = true;
  try {
    const res = await adminService.getServiceDetail(route.params.id);
    detail.value = res;
    statusUpdate.value = res.status;
    notesUpdate.value = res.admin_notes || '';
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const openLetterModal = async () => {
  if (!detail.value) return;
  isLetterLoading.value = true;
  try {
    const res = await adminService.getServiceLetter(detail.value.id);
    letterData.value = res.document || res;
    isLetterModalOpen.value = true;
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal memuat dokumen surat resmi.');
  } finally {
    isLetterLoading.value = false;
  }
};

const getPdfFilename = () => {
  const name = (detail.value?.full_name || 'Warga').replace(/[^a-zA-Z0-9]/g, '_');
  const cleanNo = (detail.value?.letter_number || detail.value?.ticket_number || 'DMS').replace(/[^a-zA-Z0-9]/g, '_');
  return `Surat_${cleanNo}_${name}.pdf`;
};

const handleDirectDownloadPdf = async () => {
  if (!detail.value?.id) return;
  isDownloadingPdf.value = true;
  const filename = getPdfFilename();
  try {
    await adminService.downloadServicePdf(detail.value.id, filename);
  } catch (err) {
    console.error('Gagal unduh file PDF:', err);
    alert('Gagal mengunduh berkas PDF resmi.');
  } finally {
    isDownloadingPdf.value = false;
  }
};

const handleSendPdfToWhatsApp = async () => {
  if (!detail.value?.id) return;
  isSendingWhatsapp.value = true;
  const filename = getPdfFilename();

  let downloadedBlob = null;
  try {
    downloadedBlob = await adminService.downloadServicePdf(detail.value.id, filename);
  } catch (err) {
    console.warn('Gagal unduh blob PDF.', err);
  }

  // Jika Web Share API dengan file didukung (misal di mobile)
  if (downloadedBlob && navigator.canShare) {
    try {
      const file = new File([downloadedBlob], filename, { type: 'application/pdf' });
      if (navigator.canShare({ files: [file] })) {
        await navigator.share({
          files: [file],
          title: `Surat Resmi Desa - ${filename}`,
          text: `Berikut kami lampirkan berkas dokumen surat resmi Anda dari Pemerintah Desa Mandiri Sejahtera.`,
        });
        isSendingWhatsapp.value = false;
        return;
      }
    } catch (shareErr) {
      console.log('Share API dialihkan ke obrolan WhatsApp Web.');
    }
  }

  // Buka chat WhatsApp pemohon
  const waUrl = detail.value?.whatsapp?.whatsapp_url;
  if (waUrl) {
    window.open(waUrl, '_blank');
  }

  saveMessage.value = `Berkas PDF "${filename}" telah otomatis terunduh ke komputer Anda. Silakan lampirkan berkas PDF tersebut ke ruang obrolan WhatsApp pemohon yang baru saja terbuka.`;
  setTimeout(() => (saveMessage.value = ''), 12000);

  isSendingWhatsapp.value = false;
};

const handleQuickApprove = async () => {
  statusUpdate.value = 'selesai';
  await handleSaveStatus();
};

const handleSaveStatus = async () => {
  if (!detail.value?.id) return;
  isSaving.value = true;
  saveMessage.value = '';
  try {
    const res = await adminService.updateServiceStatus(detail.value.id, statusUpdate.value, notesUpdate.value);
    if (res.success) {
      detail.value = res.data;
      gatewayDeliveryResult.value = res.whatsapp_gateway || null;

      if (res.data.status === 'selesai') {
        const isSentViaGateway = gatewayDeliveryResult.value?.sent_via_gateway === true;
        if (isSentViaGateway) {
          saveMessage.value = 'Status permohonan diselesaikan! Berkas PDF resmi telah berhasil dikirim langsung ke WhatsApp pemohon melalui server.';
        } else {
          saveMessage.value = 'Status permohonan diselesaikan dan surat resmi berhasil diterbitkan!';
        }

        // Jika opsi otomatis kirim WhatsApp aktif
        if (autoSendWhatsApp.value) {
          const waUrl = res.data.whatsapp?.whatsapp_url;
          const phone = res.data.phone_number || res.data.whatsapp?.raw_phone;
          const filename = getPdfFilename();

          lastDispatchedWaUrl.value = waUrl || '';
          lastDispatchedPhone.value = phone || '';
          lastDownloadedPdfName.value = filename;

          // 1. Selalu unduh dokumen PDF resmi ke komputer admin agar ada cadangan fisik lokal
          try {
            await adminService.downloadServicePdf(detail.value.id, filename);
          } catch (pdfErr) {
            console.warn('Gagal auto-download berkas PDF:', pdfErr);
          }

          // 2. Jika tidak dikirim via Gateway API, otomatis buka obrolan WhatsApp Web pemohon
          if (!isSentViaGateway && waUrl) {
            window.open(waUrl, '_blank');
          }

          // 3. Tampilkan dialog sukses konfirmasi WhatsApp & panduan pengiriman file di lokal
          isWaNotificationModalOpen.value = true;
        }
      } else {
        saveMessage.value = 'Pembaruan status dan catatan untuk warga berhasil disimpan!';
      }
      setTimeout(() => (saveMessage.value = ''), 8000);
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal memperbarui status.');
  } finally {
    isSaving.value = false;
  }
};

const previewDoc = async (id) => {
  await adminService.previewDocument(id);
};

const downloadDoc = async (id, name) => {
  await adminService.downloadDocument(id, name);
};

onMounted(() => {
  loadDetail();
});
</script>

