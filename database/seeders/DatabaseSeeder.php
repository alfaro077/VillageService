<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna Default (Super Admin & Petugas Pelayanan)
        User::updateOrCreate(
            ['email' => 'superadmin@desa.id'],
            [
                'name' => 'Kepala Desa (Super Admin)',
                'password' => Hash::make('SuperAdmin123!'),
                'role' => 'superadmin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@desa.id'],
            [
                'name' => 'Petugas Layanan Desa',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
            ]
        );

        // 2. Master Jenis Layanan Surat Online
        $serviceTypes = [
            [
                'code' => 'SKD',
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Surat keterangan bukti tempat tinggal resmi warga di wilayah desa.',
                'required_documents' => [
                    ['type' => 'ktp', 'label' => 'Foto/Scan KTP Pemohon'],
                    ['type' => 'kk', 'label' => 'Foto/Scan Kartu Keluarga'],
                    ['type' => 'surat_pengantar_rt', 'label' => 'Surat Pengantar RT/RW'],
                ],
                'is_active' => true,
            ],
            [
                'code' => 'SKTM',
                'name' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'description' => 'Surat keterangan untuk keperluan beasiswa sekolah, keringanan biaya RS, atau bantuan sosial.',
                'required_documents' => [
                    ['type' => 'ktp', 'label' => 'Foto/Scan KTP Pemohon/Ortu'],
                    ['type' => 'kk', 'label' => 'Foto/Scan Kartu Keluarga'],
                    ['type' => 'surat_pengantar_rt', 'label' => 'Surat Pengantar RT/RW'],
                ],
                'is_active' => true,
            ],
            [
                'code' => 'SKU',
                'name' => 'Surat Keterangan Usaha (SKU)',
                'description' => 'Surat keterangan untuk legalitas usaha mikro/kecil warga di desa untuk keperluan perbankan/izin.',
                'required_documents' => [
                    ['type' => 'ktp', 'label' => 'Foto/Scan KTP Pemilik Usaha'],
                    ['type' => 'kk', 'label' => 'Foto/Scan Kartu Keluarga'],
                    ['type' => 'foto_usaha', 'label' => 'Foto Tempat / Aktivitas Usaha'],
                ],
                'is_active' => true,
            ],
            [
                'code' => 'KTP_KK',
                'name' => 'Surat Pengantar Pembuatan KTP / KK',
                'description' => 'Surat pengantar untuk pengurusan pembuatan atau pembaruan KTP / Kartu Keluarga ke Disdukcapil.',
                'required_documents' => [
                    ['type' => 'kk_lama', 'label' => 'Foto/Scan KK Lama (Jika Ada)'],
                    ['type' => 'surat_pengantar_rt', 'label' => 'Surat Pengantar RT/RW'],
                ],
                'is_active' => true,
            ],
            [
                'code' => 'SKCK',
                'name' => 'Surat Pengantar SKCK',
                'description' => 'Surat pengantar permohonan Surat Keterangan Catatan Kepolisian ke Polsek/Polres.',
                'required_documents' => [
                    ['type' => 'ktp', 'label' => 'Foto/Scan KTP Pemohon'],
                    ['type' => 'kk', 'label' => 'Foto/Scan Kartu Keluarga'],
                    ['type' => 'surat_pengantar_rt', 'label' => 'Surat Pengantar RT/RW'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($serviceTypes as $service) {
            ServiceType::updateOrCreate(['code' => $service['code']], $service);
        }
    }
}
