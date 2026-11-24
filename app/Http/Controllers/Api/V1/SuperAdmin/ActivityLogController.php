<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan daftar audit trail dan log aktivitas sistem.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::latest('id');

        // Filter berdasarkan aksi
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Pencarian deskripsi atau nama pengguna
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 20);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Statistik ringkasan aktivitas sistem untuk monitoring.
     */
    public function stats(): JsonResponse
    {
        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();

        $actionBreakdown = ActivityLog::selectRaw('action, count(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        $latestLogins = ActivityLog::where('action', 'LOGIN')
            ->latest('id')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'total_logs' => $totalLogs,
            'today_logs' => $todayLogs,
            'breakdown' => $actionBreakdown,
            'latest_logins' => $latestLogins,
        ]);
    }
}
