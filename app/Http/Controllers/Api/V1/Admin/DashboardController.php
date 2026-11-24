<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Dapatkan ringkasan statistik untuk dashboard admin.
     */
    public function stats(): JsonResponse
    {
        $totalServices = ServiceRequest::count();
        $pendingServices = ServiceRequest::where('status', 'pending')->count();
        $processingServices = ServiceRequest::where('status', 'diproses')->count();
        $completedServices = ServiceRequest::where('status', 'selesai')->count();
        $rejectedServices = ServiceRequest::where('status', 'ditolak')->count();

        $totalComplaints = Complaint::count();
        $waitingComplaints = Complaint::where('status', 'menunggu')->count();
        $investigatingComplaints = Complaint::where('status', 'ditindaklanjuti')->count();
        $completedComplaints = Complaint::where('status', 'selesai')->count();

        $recentRequests = ServiceRequest::with('serviceType')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'ticket_number' => $item->ticket_number,
                'service_name' => $item->serviceType?->name,
                'applicant_name' => $item->full_name, // Terdekripsi otomatis untuk admin
                'status' => $item->status,
                'created_at' => $item->created_at->toIso8601String(),
            ]);

        $recentComplaints = Complaint::latest()
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'ticket_number' => $item->ticket_number,
                'title' => $item->complaint_title, // Terdekripsi otomatis untuk admin
                'reporter' => $item->reporter_name,
                'status' => $item->status,
                'created_at' => $item->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'summary' => [
                'services' => [
                    'total' => $totalServices,
                    'pending' => $pendingServices,
                    'processing' => $processingServices,
                    'completed' => $completedServices,
                    'rejected' => $rejectedServices,
                ],
                'complaints' => [
                    'total' => $totalComplaints,
                    'waiting' => $waitingComplaints,
                    'investigating' => $investigatingComplaints,
                    'completed' => $completedComplaints,
                ],
            ],
            'recent_requests' => $recentRequests,
            'recent_complaints' => $recentComplaints,
        ]);
    }
}
