<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\ServiceTypeResource;
use App\Models\RequestDocument;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Services\CryptoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceRequestController extends Controller
{
    public function __construct(protected CryptoService $cryptoService) {}

    /**
     * Daftar jenis layanan surat yang aktif.
     */
    public function getTypes(): JsonResponse
    {
        $types = ServiceType::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => ServiceTypeResource::collection($types),
        ]);
    }

    /**
     * Submit pengajuan layanan online oleh warga.
     */
    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_type_id' => ['required', 'exists:service_types,id'],
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'kk_number' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'min:8', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'purpose_description' => ['nullable', 'string', 'max:2000'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'document_types' => ['nullable', 'array'],
            'document_types.*' => ['string', 'max:50'],
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Generate nomor tiket unik: format SRV-YYYYMM-XXXXXX
                do {
                    $randomPart = strtoupper(Str::random(6));
                    $ticketNumber = 'SRV-'.date('Ym').'-'.$randomPart;
                } while (ServiceRequest::where('ticket_number', $ticketNumber)->exists());

                // Simpan pengajuan dengan At-Rest Encryption (AES-256 otomatis oleh Model Casts)
                $serviceRequest = ServiceRequest::create([
                    'ticket_number' => $ticketNumber,
                    'service_type_id' => $validated['service_type_id'],
                    'nik' => $validated['nik'],
                    'kk_number' => $validated['kk_number'],
                    'full_name' => $validated['full_name'],
                    'phone_number' => $validated['phone_number'],
                    'address' => $validated['address'],
                    'purpose_description' => $validated['purpose_description'] ?? null,
                    'nik_blind_index' => $this->cryptoService->blindIndex($validated['nik']),
                    'status' => 'pending',
                ]);

                // Simpan dokumen lampiran ke storage/app/private (bukan direktori publik)
                if ($request->hasFile('documents')) {
                    $files = $request->file('documents');
                    $docTypes = $request->input('document_types', []);

                    foreach ($files as $index => $file) {
                        $docType = $docTypes[$index] ?? 'dokumen_pendukung';
                        $extension = $file->getClientOriginalExtension();
                        $sanitizedName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $fileName = $ticketNumber.'_'.$docType.'_'.Str::random(8).'.'.$extension;

                        // Simpan file ke direktori privat
                        $filePath = $file->storeAs('documents/'.$ticketNumber, $fileName, 'private');

                        RequestDocument::create([
                            'attachable_type' => ServiceRequest::class,
                            'attachable_id' => $serviceRequest->id,
                            'document_type' => $docType,
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_mime' => $file->getMimeType() ?: 'application/octet-stream',
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan surat berhasil dikirim. Simpan nomor tiket Anda untuk melacak status.',
                    'ticket_number' => $ticketNumber,
                ], 201);
            });
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pengajuan: '.$e->getMessage(),
            ], 500);
        }
    }
}
