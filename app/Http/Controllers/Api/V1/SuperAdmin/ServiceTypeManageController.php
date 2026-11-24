<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ServiceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceTypeManageController extends Controller
{
    /**
     * Tampilkan seluruh jenis layanan surat (aktif & nonaktif) beserta statistik pengajuan.
     */
    public function index(): JsonResponse
    {
        $types = ServiceType::withCount('requests')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    /**
     * Tambah jenis layanan surat baru ke sistem.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'uppercase', 'unique:service_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'required_documents' => ['nullable', 'array'],
            'required_documents.*.type' => ['required_with:required_documents', 'string'],
            'required_documents.*.label' => ['required_with:required_documents', 'string'],
            'is_active' => ['boolean'],
        ]);

        $serviceType = ServiceType::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'required_documents' => $validated['required_documents'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        ActivityLog::log(
            $request,
            'CREATE_SERVICE_TYPE',
            "Menambahkan jenis layanan surat baru: {$serviceType->name} ({$serviceType->code})",
            $serviceType
        );

        return response()->json([
            'success' => true,
            'message' => "Jenis layanan '{$serviceType->name}' berhasil ditambahkan.",
            'data' => $serviceType,
        ], 201);
    }

    /**
     * Perbarui data atau syarat dokumen jenis layanan.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $serviceType = ServiceType::find($id);

        if (! $serviceType) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis layanan tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'uppercase', Rule::unique('service_types', 'code')->ignore($serviceType->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'required_documents' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $serviceType->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'required_documents' => $validated['required_documents'] ?? $serviceType->required_documents,
            'is_active' => $validated['is_active'] ?? $serviceType->is_active,
        ]);

        ActivityLog::log(
            $request,
            'UPDATE_SERVICE_TYPE',
            "Memperbarui jenis layanan surat: {$serviceType->name} ({$serviceType->code})",
            $serviceType
        );

        return response()->json([
            'success' => true,
            'message' => "Jenis layanan '{$serviceType->name}' berhasil diperbarui.",
            'data' => $serviceType,
        ]);
    }

    /**
     * Aktifkan / Nonaktifkan layanan surat.
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $serviceType = ServiceType::find($id);

        if (! $serviceType) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis layanan tidak ditemukan.',
            ], 404);
        }

        $serviceType->is_active = ! $serviceType->is_active;
        $serviceType->save();

        $statusStr = $serviceType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log(
            $request,
            'TOGGLE_SERVICE_TYPE',
            "Status jenis layanan {$serviceType->name} diubah menjadi {$statusStr}",
            $serviceType
        );

        return response()->json([
            'success' => true,
            'message' => "Layanan '{$serviceType->name}' berhasil {$statusStr}.",
            'data' => $serviceType,
        ]);
    }

    /**
     * Hapus jenis layanan jika belum ada riwayat pengajuan terkait.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $serviceType = ServiceType::withCount('requests')->find($id);

        if (! $serviceType) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis layanan tidak ditemukan.',
            ], 404);
        }

        if ($serviceType->requests_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Layanan tidak dapat dihapus karena sudah memiliki {$serviceType->requests_count} pengajuan terkait. Anda dapat menonaktifkannya saja.",
            ], 422);
        }

        $name = $serviceType->name;
        $serviceType->delete();

        ActivityLog::log(
            $request,
            'DELETE_SERVICE_TYPE',
            "Menghapus jenis layanan: {$name}",
            null
        );

        return response()->json([
            'success' => true,
            'message' => "Layanan '{$name}' berhasil dihapus dari sistem.",
        ]);
    }
}
