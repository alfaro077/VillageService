<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminComplaintResource;
use App\Models\ActivityLog;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintsManageController extends Controller
{
    /**
     * Tampilkan daftar pengaduan warga.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Complaint::with(['processor', 'documents'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where('ticket_number', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 15);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AdminComplaintResource::collection($paginated->items()),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Tampilkan detail pengaduan terdekripsi untuk Admin.
     */
    public function show(int $id): JsonResponse
    {
        $complaint = Complaint::with(['processor', 'documents'])->find($id);

        if (! $complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan pengaduan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AdminComplaintResource($complaint),
        ]);
    }

    /**
     * Update status dan tanggapan admin atas pengaduan warga.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,ditindaklanjuti,selesai,ditolak'],
            'admin_response' => ['nullable', 'string', 'max:2000'],
        ]);

        $complaint = Complaint::find($id);

        if (! $complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan pengaduan tidak ditemukan.',
            ], 404);
        }

        $complaint->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'] ?? $complaint->admin_response,
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        ActivityLog::log(
            $request,
            'UPDATE_COMPLAINT_STATUS',
            "Memperbarui status pengaduan {$complaint->ticket_number} menjadi '{$validated['status']}'",
            $complaint
        );

        return response()->json([
            'success' => true,
            'message' => 'Status pengaduan berhasil diperbarui.',
            'data' => new AdminComplaintResource($complaint->fresh(['processor', 'documents'])),
        ]);
    }
}
