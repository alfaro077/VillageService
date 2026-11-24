<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\RequestDocument;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    /**
     * Submit form pengaduan/aspirasi warga.
     */
    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_nik' => ['nullable', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'reporter_phone' => ['nullable', 'string', 'min:8', 'max:20'],
            'complaint_title' => ['required', 'string', 'max:255'],
            'complaint_content' => ['required', 'string', 'max:4000'],
            'location' => ['nullable', 'string', 'max:500'],
            'category' => ['required', 'string', 'max:100'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Generate nomor tiket pengaduan unik: ADU-YYYYMM-XXXXXX
                do {
                    $randomPart = strtoupper(Str::random(6));
                    $ticketNumber = 'ADU-'.date('Ym').'-'.$randomPart;
                } while (Complaint::where('ticket_number', $ticketNumber)->exists());

                // Simpan pengaduan dengan enkripsi otomatis At-Rest
                $complaint = Complaint::create([
                    'ticket_number' => $ticketNumber,
                    'reporter_name' => $validated['reporter_name'],
                    'reporter_nik' => $validated['reporter_nik'] ?? null,
                    'reporter_phone' => $validated['reporter_phone'] ?? null,
                    'complaint_title' => $validated['complaint_title'],
                    'complaint_content' => $validated['complaint_content'],
                    'location' => $validated['location'] ?? null,
                    'category' => $validated['category'],
                    'status' => 'menunggu',
                ]);

                // Simpan file bukti ke direktori privat
                if ($request->hasFile('documents')) {
                    foreach ($request->file('documents') as $file) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $ticketNumber.'_bukti_'.Str::random(8).'.'.$extension;
                        $filePath = $file->storeAs('documents/'.$ticketNumber, $fileName, 'private');

                        RequestDocument::create([
                            'attachable_type' => Complaint::class,
                            'attachable_id' => $complaint->id,
                            'document_type' => 'bukti_pengaduan',
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_mime' => $file->getMimeType() ?: 'application/octet-stream',
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Laporan pengaduan berhasil dikirim. Simpan nomor tiket ini untuk melacak tindak lanjut.',
                    'ticket_number' => $ticketNumber,
                ], 201);
            });
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pengaduan: '.$e->getMessage(),
            ], 500);
        }
    }
}
