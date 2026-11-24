<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Services\CryptoService;
use Illuminate\Http\JsonResponse;

class SecurityController extends Controller
{
    public function __construct(protected CryptoService $cryptoService) {}

    /**
     * Berikan Public Key RSA server ke browser untuk enkripsi data di sisi klien (In-Transit).
     */
    public function getPublicKey(): JsonResponse
    {
        $publicKey = $this->cryptoService->getPublicKeyPem();

        return response()->json([
            'success' => true,
            'public_key' => $publicKey,
            'algorithm' => 'RSA-OAEP-SHA1',
        ]);
    }
}
