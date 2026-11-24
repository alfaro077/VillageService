<?php

namespace Tests\Feature;

use App\Models\RequestDocument;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\CryptoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test endpoint public key untuk enkripsi klien.
     */
    public function test_can_get_rsa_public_key(): void
    {
        $response = $this->getJson('/api/v1/security/public-key');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'public_key', 'algorithm']);

        $this->assertStringContainsString('BEGIN PUBLIC KEY', $response->json('public_key'));
    }

    /**
     * Test mendapatkan daftar jenis layanan.
     */
    public function test_can_get_active_service_types(): void
    {
        $response = $this->getJson('/api/v1/public/service-types');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(5, 'data');
    }

    /**
     * Test submit pengajuan surat warga dan verifikasi enkripsi at-rest di database.
     */
    public function test_citizen_can_submit_service_request_and_data_is_encrypted_at_rest(): void
    {
        Storage::fake('private');

        $serviceType = ServiceType::first();
        $plainNik = '3201123456789012';
        $plainName = 'Budi Santoso';

        $file = UploadedFile::fake()->create('ktp.jpg', 200, 'image/jpeg');

        $response = $this->postJson('/api/v1/public/services', [
            'service_type_id' => $serviceType->id,
            'nik' => $plainNik,
            'kk_number' => '3201987654321098',
            'full_name' => $plainName,
            'phone_number' => '081234567890',
            'address' => 'RT 02 RW 01 Dusun Mawar',
            'purpose_description' => 'Untuk melengkapi berkas administrasi pekerjaan',
            'documents' => [$file],
            'document_types' => ['ktp'],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'ticket_number']);

        $ticket = $response->json('ticket_number');
        $this->assertNotNull($ticket);

        // 1. Verifikasi data di tabel MySQL: Harus ciphertext, BUKAN plaintext
        $rawRecord = DB::table('service_requests')->where('ticket_number', $ticket)->first();
        $this->assertNotNull($rawRecord);
        $this->assertNotEquals($plainNik, $rawRecord->nik, 'NIK harus tersimpan dalam bentuk terenkripsi!');
        $this->assertNotEquals($plainName, $rawRecord->full_name, 'Nama lengkap harus tersimpan dalam bentuk terenkripsi!');

        // 2. Verifikasi Model Eloquent dapat mendekripsinya secara transparan saat dipanggil melalui model
        $model = ServiceRequest::where('ticket_number', $ticket)->first();
        $this->assertEquals($plainNik, $model->nik);
        $this->assertEquals($plainName, $model->full_name);

        // 3. Verifikasi file lampiran tersimpan di direktori privat
        $this->assertCount(1, $model->documents);
        Storage::disk('private')->assertExists($model->documents->first()->file_path);
    }

    /**
     * Test submit dengan payload terenkripsi in-transit (simulasi Web Crypto API / RSA Hybrid frontend).
     */
    public function test_can_submit_service_request_with_in_transit_client_encryption(): void
    {
        $cryptoService = app(CryptoService::class);
        $publicKeyPem = $cryptoService->getPublicKeyPem();

        $serviceType = ServiceType::first();

        // Data rahasia yang dienkripsi klien
        $secretFormData = [
            'service_type_id' => $serviceType->id,
            'nik' => '3201555566667777',
            'kk_number' => '3201888899990000',
            'full_name' => 'Dewi Lestari',
            'phone_number' => '089912345678',
            'address' => 'Kampung Harapan RT 03',
            'purpose_description' => 'Pengantar nikah',
        ];

        // 1. Klien generate AES Session Key acak (32 bytes) & IV (12 bytes untuk GCM)
        $aesSessionKey = random_bytes(32);
        $iv = random_bytes(12);

        // 2. Klien enkripsi payload JSON dengan AES-256-GCM
        $ciphertext = openssl_encrypt(json_encode($secretFormData), 'aes-256-gcm', $aesSessionKey, OPENSSL_RAW_DATA, $iv, $tag);

        // 3. Klien enkripsi AES Session Key dengan RSA Public Key Server
        $serverPublicKey = openssl_pkey_get_public($publicKeyPem);
        openssl_public_encrypt($aesSessionKey, $encryptedKey, $serverPublicKey, OPENSSL_PKCS1_OAEP_PADDING);

        // 4. Kirim payload terenkripsi
        $response = $this->postJson('/api/v1/public/services', [
            '_encrypted' => true,
            'encrypted_key' => base64_encode($encryptedKey),
            'iv' => base64_encode($iv),
            'data' => base64_encode($ciphertext),
            'tag' => base64_encode($tag),
            'mode' => 'aes-256-gcm',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $ticket = $response->json('ticket_number');
        $record = ServiceRequest::where('ticket_number', $ticket)->first();
        $this->assertNotNull($record);
        $this->assertEquals('Dewi Lestari', $record->full_name);
        $this->assertEquals('3201555566667777', $record->nik);
    }

    /**
     * Test endpoint lacak status tiket publik TIDAK membocorkan NIK atau data pribadi warga.
     */
    public function test_tracking_endpoint_does_not_leak_citizen_pii(): void
    {
        $serviceType = ServiceType::first();

        $serviceRequest = ServiceRequest::create([
            'ticket_number' => 'SRV-202611-TEST01',
            'service_type_id' => $serviceType->id,
            'nik' => '3201123456789012',
            'kk_number' => '3201987654321098',
            'full_name' => 'Budi Santoso Rahasia',
            'phone_number' => '081234567890',
            'address' => 'Alamat Sangat Rahasia',
            'purpose_description' => 'Keterangan Rahasia',
            'status' => 'diproses',
            'admin_notes' => 'Sedang diverifikasi oleh kasi pelayanan',
        ]);

        $response = $this->getJson('/api/v1/public/track/'.$serviceRequest->ticket_number);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ticket_number', 'SRV-202611-TEST01')
            ->assertJsonPath('data.status', 'diproses')
            ->assertJsonPath('data.notes', 'Sedang diverifikasi oleh kasi pelayanan');

        // Pastikan TIDAK ADA field sensitif sama sekali di response json
        $responseContent = $response->getContent();
        $this->assertStringNotContainsString('3201123456789012', $responseContent);
        $this->assertStringNotContainsString('Budi Santoso Rahasia', $responseContent);
        $this->assertStringNotContainsString('Alamat Sangat Rahasia', $responseContent);
        $this->assertArrayNotHasKey('nik', $response->json('data'));
        $this->assertArrayNotHasKey('full_name', $response->json('data'));
    }

    /**
     * Test login admin dan akses data terdekripsi via endpoint admin.
     */
    public function test_admin_can_login_and_access_decrypted_data(): void
    {
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@desa.id',
            'password' => 'Admin123!',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['token', 'user']);

        $token = $loginResponse->json('token');

        // Buat pengajuan layanan
        $serviceType = ServiceType::first();
        $request = ServiceRequest::create([
            'ticket_number' => 'SRV-202611-ADM001',
            'service_type_id' => $serviceType->id,
            'nik' => '3201123456789012',
            'kk_number' => '3201987654321098',
            'full_name' => 'Siti Aisyah',
            'phone_number' => '081299887766',
            'address' => 'Jl. Merdeka No. 45',
            'purpose_description' => 'Pembuatan KTP baru',
            'status' => 'pending',
        ]);

        // Coba akses endpoint admin tanpa token => 401 Unauthorized
        $unauthResponse = $this->getJson('/api/v1/admin/services/'.$request->id);
        $unauthResponse->assertStatus(401);

        // Akses endpoint admin dengan token => 200 OK dengan data didekripsi
        $authResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/services/'.$request->id);

        $authResponse->assertStatus(200)
            ->assertJsonPath('data.nik', '3201123456789012')
            ->assertJsonPath('data.full_name', 'Siti Aisyah')
            ->assertJsonPath('data.phone_number', '081299887766');

        // Update status pengajuan
        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/services/'.$request->id.'/status', [
                'status' => 'diproses',
                'admin_notes' => 'Dokumen sudah lengkap, menunggu penandatanganan kepala desa.',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'diproses')
            ->assertJsonPath('data.admin_notes', 'Dokumen sudah lengkap, menunggu penandatanganan kepala desa.');
    }

    /**
     * Test upload dan akses file lampiran privat terlindungi otentikasi admin.
     */
    public function test_attachment_download_is_protected(): void
    {
        Storage::fake('private');

        $docPath = 'documents/SRV-202611-TEST/ktp.jpg';
        Storage::disk('private')->put($docPath, 'FAKE_IMAGE_DATA');

        $serviceRequest = ServiceRequest::create([
            'ticket_number' => 'SRV-202611-TEST99',
            'service_type_id' => ServiceType::first()->id,
            'nik' => '3201123456789012',
            'kk_number' => '3201987654321098',
            'full_name' => 'Warga Uji',
            'phone_number' => '081234567890',
            'address' => 'Dusun 1',
            'status' => 'pending',
        ]);

        $document = RequestDocument::create([
            'attachable_type' => ServiceRequest::class,
            'attachable_id' => $serviceRequest->id,
            'document_type' => 'ktp',
            'original_name' => 'ktp_asli.jpg',
            'file_path' => $docPath,
            'file_mime' => 'image/jpeg',
            'file_size' => 1024,
        ]);

        // 1. Akses tanpa token => 401
        $this->getJson("/api/v1/admin/documents/{$document->id}/view")->assertStatus(401);
        $this->getJson("/api/v1/admin/documents/{$document->id}/download")->assertStatus(401);

        // 2. Akses dengan admin token => 200 OK
        $admin = User::where('email', 'admin@desa.id')->first();
        $token = $admin->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get("/api/v1/admin/documents/{$document->id}/view");

        $response->assertStatus(200);
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));
    }

    /**
     * Test staf admin biasa tidak dapat mengakses endpoint superadmin (403 Forbidden).
     */
    public function test_normal_admin_cannot_access_superadmin_endpoints(): void
    {
        $admin = User::where('email', 'admin@desa.id')->first();
        $token = $admin->createToken('admin_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/superadmin/service-types');

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /**
     * Test superadmin dapat mengelola master jenis layanan dan aktivitas tercatat di audit log.
     */
    public function test_superadmin_can_manage_service_types_and_activity_is_logged(): void
    {
        $superadmin = User::where('email', 'superadmin@desa.id')->first();
        $token = $superadmin->createToken('superadmin_token')->plainTextToken;

        // 1. Tambah jenis layanan baru
        $createResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/superadmin/service-types', [
                'code' => 'SKP',
                'name' => 'Surat Keterangan Penghasilan',
                'description' => 'Surat keterangan jumlah penghasilan orang tua untuk pendaftaran kuliah.',
                'required_documents' => [
                    ['type' => 'slip_gaji', 'label' => 'Slip Gaji / Bukti Usaha'],
                    ['type' => 'ktp', 'label' => 'KTP Pemohon'],
                ],
                'is_active' => true,
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.code', 'SKP');

        $serviceTypeId = $createResponse->json('data.id');

        // 2. Toggle status aktif
        $toggleResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/superadmin/service-types/'.$serviceTypeId.'/toggle');

        $toggleResponse->assertStatus(200)
            ->assertJsonPath('data.is_active', false);

        // 3. Verifikasi Log Aktivitas tercatat
        $logsResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/superadmin/activity-logs');

        $logsResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'CREATE_SERVICE_TYPE',
            'user_role' => 'superadmin',
        ]);
    }

    /**
     * Test superadmin dapat mengelola pengguna admin.
     */
    public function test_superadmin_can_manage_admin_users(): void
    {
        $superadmin = User::where('email', 'superadmin@desa.id')->first();
        $token = $superadmin->createToken('superadmin_token')->plainTextToken;

        // Buat admin baru
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/superadmin/users', [
                'name' => 'Petugas Baru',
                'email' => 'petugas.baru@desa.id',
                'password' => 'Password123!',
                'role' => 'admin',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'petugas.baru@desa.id');

        $this->assertDatabaseHas('users', [
            'email' => 'petugas.baru@desa.id',
            'role' => 'admin',
        ]);
    }

    /**
     * Test login sebagai superadmin melalui endpoint auth API.
     */
    public function test_superadmin_can_login_via_auth_api(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@desa.id',
            'password' => 'SuperAdmin123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.email', 'superadmin@desa.id')
            ->assertJsonPath('user.role', 'superadmin')
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
    }
}
