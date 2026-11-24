<?php

namespace App\Http\Middleware;

use App\Services\CryptoService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DecryptClientPayload
{
    public function __construct(protected CryptoService $cryptoService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('_encrypted')) {
            $decrypted = $this->cryptoService->decryptClientPayload($request->all());
            $request->merge($decrypted);
        }

        return $next($request);
    }
}
