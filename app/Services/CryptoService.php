<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CryptoService
{
    protected string $privateKeyPath;

    protected string $publicKeyPath;

    public function __construct()
    {
        $keysDir = storage_path('app/keys');
        $this->privateKeyPath = $keysDir.'/rsa_private.key';
        $this->publicKeyPath = $keysDir.'/rsa_public.key';

        $this->ensureKeysExist();
    }

    /**
     * Pastikan pasangan kunci RSA tersedia di storage/app/keys.
     */
    public function ensureKeysExist(): void
    {
        $keysDir = dirname($this->privateKeyPath);
        if (! File::isDirectory($keysDir)) {
            File::makeDirectory($keysDir, 0700, true);
        }

        if (! File::exists($this->privateKeyPath) || ! File::exists($this->publicKeyPath)) {
            $cnfCandidates = [
                getenv('OPENSSL_CONF'),
                dirname(PHP_BINARY).'/extras/ssl/openssl.cnf',
                'C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/extras/ssl/openssl.cnf',
                'C:/laragon/bin/php/php-8.1.10-Win32-vs16-x64/extras/ssl/openssl.cnf',
            ];

            $config = [
                'digest_alg' => 'sha256',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            foreach ($cnfCandidates as $cnf) {
                if ($cnf && File::exists($cnf)) {
                    $config['config'] = $cnf;
                    putenv("OPENSSL_CONF={$cnf}");
                    break;
                }
            }

            $res = openssl_pkey_new($config);
            if ($res === false) {
                // Ambil error openssl jika ada
                $errorStr = '';
                while ($msg = openssl_error_string()) {
                    $errorStr .= $msg.' | ';
                }
                throw new Exception('Gagal membuat pasangan kunci RSA: '.$errorStr);
            }

            openssl_pkey_export($res, $privateKey, null, $config);
            $keyDetails = openssl_pkey_get_details($res);
            $publicKey = $keyDetails['key'] ?? '';

            File::put($this->privateKeyPath, $privateKey);
            File::put($this->publicKeyPath, $publicKey);
        }
    }

    /**
     * Dapatkan Public Key RSA dalam format PEM untuk dikirim ke frontend.
     */
    public function getPublicKeyPem(): string
    {
        if (! File::exists($this->publicKeyPath)) {
            $this->ensureKeysExist();
        }

        return File::get($this->publicKeyPath);
    }

    /**
     * Dapatkan Private Key RSA dalam format PEM.
     */
    protected function getPrivateKeyPem(): string
    {
        if (! File::exists($this->privateKeyPath)) {
            $this->ensureKeysExist();
        }

        return File::get($this->privateKeyPath);
    }

    /**
     * Hitung blind index (HMAC-SHA256) untuk pencarian cepat data terenkripsi.
     */
    public function blindIndex(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $appKey = config('app.key');
        if (str_starts_with((string) $appKey, 'base64:')) {
            $appKey = base64_decode(substr((string) $appKey, 7));
        }

        return hash_hmac('sha256', trim((string) $value), (string) $appKey);
    }

    /**
     * Dekripsi hybrid payload yang dikirim dari Frontend.
     * Mendukung enkripsi AES-256-GCM / AES-256-CBC dengan Session Key terenkripsi RSA.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function decryptClientPayload(array $payload): array
    {
        $isEncrypted = filter_var($payload['_encrypted'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (! $isEncrypted || empty($payload['encrypted_key']) || empty($payload['data'])) {
            return $payload;
        }

        try {
            $privateKey = openssl_pkey_get_private($this->getPrivateKeyPem());
            if (! $privateKey) {
                throw new Exception('Kunci privat RSA tidak valid.');
            }

            $encryptedSessionKey = base64_decode((string) $payload['encrypted_key']);
            $decryptedSessionKey = '';

            // Coba dengan OPENSSL_PKCS1_OAEP_PADDING (Web Crypto API default)
            $decrypted = openssl_private_decrypt(
                $encryptedSessionKey,
                $decryptedSessionKey,
                $privateKey,
                OPENSSL_PKCS1_OAEP_PADDING
            );

            // Fallback ke OPENSSL_PKCS1_PADDING jika OAEP gagal
            if (! $decrypted) {
                $decrypted = openssl_private_decrypt(
                    $encryptedSessionKey,
                    $decryptedSessionKey,
                    $privateKey,
                    OPENSSL_PKCS1_PADDING
                );
            }

            if (! $decrypted) {
                throw new Exception('Gagal mendekripsi Session Key dari payload.');
            }

            $ciphertext = base64_decode((string) $payload['data']);
            $iv = base64_decode((string) ($payload['iv'] ?? ''));
            $tag = ! empty($payload['tag']) ? base64_decode((string) $payload['tag']) : '';
            $mode = (string) ($payload['mode'] ?? 'aes-256-gcm');

            $decryptedJson = false;
            if ($mode === 'aes-256-gcm' && ! empty($tag)) {
                $decryptedJson = openssl_decrypt(
                    $ciphertext,
                    'aes-256-gcm',
                    $decryptedSessionKey,
                    OPENSSL_RAW_DATA,
                    $iv,
                    $tag
                );
            }

            if ($decryptedJson === false && strlen($iv) === 16) {
                $decryptedJson = openssl_decrypt(
                    $ciphertext,
                    'aes-256-cbc',
                    $decryptedSessionKey,
                    OPENSSL_RAW_DATA,
                    $iv
                );
            }

            if ($decryptedJson === false) {
                throw new Exception('Gagal mendekripsi isi payload data.');
            }

            $decodedData = json_decode($decryptedJson, true);
            if (! is_array($decodedData)) {
                throw new Exception('Format hasil dekripsi bukan JSON valid.');
            }

            // Gabungkan data terdekripsi dengan payload lain (misal token, file upload, dsb.)
            unset($payload['_encrypted'], $payload['encrypted_key'], $payload['data'], $payload['iv'], $payload['tag'], $payload['mode']);

            return array_merge($payload, $decodedData);
        } catch (Exception $e) {
            Log::warning('Gagal mendekripsi client payload: '.$e->getMessage());

            return $payload;
        }
    }
}
