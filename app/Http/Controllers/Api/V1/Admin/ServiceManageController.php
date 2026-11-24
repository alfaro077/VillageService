<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminServiceRequestResource;
use App\Models\ActivityLog;
use App\Models\ServiceRequest;
use App\Services\CryptoService;
use App\Services\OfficialLetterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceManageController extends Controller
{
    public function __construct(
        protected CryptoService $cryptoService,
        protected OfficialLetterService $letterService
    ) {}

    /**
     * Tampilkan daftar pengajuan surat (dengan paginasi dan filter).
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceRequest::with(['serviceType', 'processor', 'documents'])->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter berdasarkan jenis layanan
        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->input('service_type_id'));
        }

        // Pencarian berdasarkan nomor tiket atau NIK (menggunakan blind index)
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $blindIndex = $this->cryptoService->blindIndex($search);

            $query->where(function ($q) use ($search, $blindIndex) {
                $q->where('ticket_number', 'like', "%{$search}%");
                if ($blindIndex) {
                    $q->orWhere('nik_blind_index', $blindIndex);
                }
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AdminServiceRequestResource::collection($paginated->items()),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Tampilkan detail data pengajuan warga yang telah didekripsi khusus untuk Admin.
     */
    public function show(int $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::with(['serviceType', 'processor', 'documents'])->find($id);

        if (! $serviceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan surat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AdminServiceRequestResource($serviceRequest),
        ]);
    }

    /**
     * Perbarui status pengajuan dan catatan petugas untuk warga.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,diproses,selesai,ditolak'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $serviceRequest = ServiceRequest::find($id);

        if (! $serviceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan surat tidak ditemukan.',
            ], 404);
        }

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $serviceRequest->admin_notes,
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ];

        // Jika status selesai, otomatis terbitkan nomor surat resmi jika belum ada
        if ($validated['status'] === 'selesai') {
            if (empty($serviceRequest->letter_number)) {
                $updateData['letter_number'] = $this->letterService->generateLetterNumber($serviceRequest);
            }
            if (empty($serviceRequest->letter_generated_at)) {
                $updateData['letter_generated_at'] = now();
            }
        }

        $serviceRequest->update($updateData);

        ActivityLog::log(
            $request,
            'UPDATE_SERVICE_STATUS',
            "Memperbarui status pengajuan {$serviceRequest->ticket_number} menjadi '{$validated['status']}'",
            $serviceRequest
        );

        $fresh = $serviceRequest->fresh(['serviceType', 'processor', 'documents']);

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan berhasil diperbarui.',
            'data' => new AdminServiceRequestResource($fresh),
        ]);
    }

    /**
     * Dapatkan data lengkap dokumen surat resmi dan link WhatsApp untuk dicetak/dikirim.
     */
    public function getLetter(Request $request, int $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::with(['serviceType', 'processor'])->find($id);

        if (! $serviceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan surat tidak ditemukan.',
            ], 404);
        }

        // Jika belum memiliki nomor surat (misal baru mau dicetak), generate nomor surat
        if (empty($serviceRequest->letter_number)) {
            $serviceRequest->update([
                'letter_number' => $this->letterService->generateLetterNumber($serviceRequest),
                'letter_generated_at' => now(),
            ]);
        }

        $letterDoc = $this->letterService->buildLetterDocument($serviceRequest);
        $whatsapp = $this->letterService->buildWhatsAppNotification($serviceRequest);

        return response()->json([
            'success' => true,
            'data' => array_merge($letterDoc, [
                'document' => $letterDoc,
                'whatsapp' => $whatsapp,
            ]),
        ]);
    }
}
