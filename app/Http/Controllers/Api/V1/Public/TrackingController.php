<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\PublicTrackingResource;
use App\Models\Complaint;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    /**
     * Lacak status pengajuan surat atau pengaduan warga via nomor tiket.
     * DILARANG KERAS mengembalikan data pribadi warga (NIK, Nama, KK, Alamat, dll).
     */
    public function track(string $ticket): JsonResponse
    {
        $cleanTicket = strtoupper(trim($ticket));

        // Cek pengajuan layanan surat
        if (str_starts_with($cleanTicket, 'SRV-')) {
            $serviceRequest = ServiceRequest::with('serviceType')
                ->where('ticket_number', $cleanTicket)
                ->first();

            if (! $serviceRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor tiket pengajuan surat tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PublicTrackingResource($serviceRequest),
            ]);
        }

        // Cek pengaduan warga
        if (str_starts_with($cleanTicket, 'ADU-')) {
            $complaint = Complaint::where('ticket_number', $cleanTicket)->first();

            if (! $complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor tiket pengaduan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PublicTrackingResource($complaint),
            ]);
        }

        // Jika awalan tiket umum
        $item = ServiceRequest::with('serviceType')->where('ticket_number', $cleanTicket)->first()
            ?? Complaint::where('ticket_number', $cleanTicket)->first();

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor tiket tidak valid atau tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PublicTrackingResource($item),
        ]);
    }
}
