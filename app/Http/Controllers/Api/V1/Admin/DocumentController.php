<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RequestDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    /**
     * Preview dokumen privat warga (KTP/KK) inline di browser Admin.
     */
    public function view(Request $request, int $id): BinaryFileResponse|JsonResponse
    {
        $document = RequestDocument::find($id);

        if (! $document) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan.',
            ], 404);
        }

        if (! Storage::disk('private')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas fisik dokumen tidak ditemukan di direktori aman.',
            ], 404);
        }

        ActivityLog::log(
            $request,
            'VIEW_DOCUMENT',
            "Melihat berkas lampiran privat '{$document->original_name}' (ID: {$document->id})",
            $document
        );

        $absolutePath = Storage::disk('private')->path($document->file_path);

        return response()->file($absolutePath, [
            'Content-Type' => $document->file_mime,
            'Content-Disposition' => 'inline; filename="'.addslashes($document->original_name).'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
        ]);
    }

    /**
     * Unduh file lampiran privat warga.
     */
    public function download(Request $request, int $id): BinaryFileResponse|JsonResponse
    {
        $document = RequestDocument::find($id);

        if (! $document) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan.',
            ], 404);
        }

        if (! Storage::disk('private')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas fisik dokumen tidak ditemukan di direktori aman.',
            ], 404);
        }

        ActivityLog::log(
            $request,
            'DOWNLOAD_DOCUMENT',
            "Mengunduh berkas lampiran privat '{$document->original_name}' (ID: {$document->id})",
            $document
        );

        $absolutePath = Storage::disk('private')->path($document->file_path);

        return response()->download($absolutePath, $document->original_name, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
        ]);
    }
}
