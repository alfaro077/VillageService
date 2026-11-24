<?php

use App\Http\Controllers\Api\V1\Admin\ComplaintsManageController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\DocumentController;
use App\Http\Controllers\Api\V1\Admin\ServiceManageController;
use App\Http\Controllers\Api\V1\Auth\AdminAuthController;
use App\Http\Controllers\Api\V1\Public\ComplaintController;
use App\Http\Controllers\Api\V1\Public\SecurityController;
use App\Http\Controllers\Api\V1\Public\ServiceRequestController;
use App\Http\Controllers\Api\V1\Public\TrackingController;
use App\Http\Controllers\Api\V1\SuperAdmin\ActivityLogController;
use App\Http\Controllers\Api\V1\SuperAdmin\ServiceTypeManageController;
use App\Http\Controllers\Api\V1\SuperAdmin\UserManageController;
use App\Http\Middleware\DecryptClientPayload;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Pelayanan Desa Online (REST API murni)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // 1. Endpoint Keamanan & Kunci Publik Enkripsi Client
    Route::get('/security/public-key', [SecurityController::class, 'getPublicKey']);

    // 2. Endpoint Portal Publik Warga (Diproteksi Throttle & Dekripsi Payload Klien)
    Route::prefix('public')->group(function () {
        Route::get('/service-types', [ServiceRequestController::class, 'getTypes']);

        // Form Submission diproteksi Rate Limit (15 per menit) dan Middleware Dekripsi In-Transit
        Route::middleware(['throttle:15,1', DecryptClientPayload::class])->group(function () {
            Route::post('/services', [ServiceRequestController::class, 'submit']);
            Route::post('/complaints', [ComplaintController::class, 'submit']);
        });

        // Lacak status tiket warga (Strictly No PII)
        Route::middleware('throttle:30,1')->group(function () {
            Route::get('/track/{ticket}', [TrackingController::class, 'track']);
            Route::get('/track/{ticket}/letter', [TrackingController::class, 'getPublicLetter']);
        });
    });

    // 3. Autentikasi Admin
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/me', [AdminAuthController::class, 'me']);
        });
    });

    // 4. Panel Admin Terproteksi (auth:sanctum & admin check)
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        // Dashboard Stats
        Route::get('/dashboard', [DashboardController::class, 'stats']);

        // Manajemen Pengajuan Layanan Surat
        Route::get('/services', [ServiceManageController::class, 'index']);
        Route::get('/services/{id}', [ServiceManageController::class, 'show']);
        Route::get('/services/{id}/letter', [ServiceManageController::class, 'getLetter']);
        Route::patch('/services/{id}/status', [ServiceManageController::class, 'updateStatus']);

        // Manajemen Pengaduan Warga
        Route::get('/complaints', [ComplaintsManageController::class, 'index']);
        Route::get('/complaints/{id}', [ComplaintsManageController::class, 'show']);
        Route::patch('/complaints/{id}/status', [ComplaintsManageController::class, 'updateStatus']);

        // Akses Aman Dokumen Privat Lampiran
        Route::get('/documents/{id}/view', [DocumentController::class, 'view']);
        Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
    });

    // 5. Panel SuperAdmin Terproteksi (auth:sanctum & superadmin check)
    Route::prefix('superadmin')->middleware(['auth:sanctum', 'superadmin'])->group(function () {
        // Kelola Master Jenis Layanan Surat
        Route::get('/service-types', [ServiceTypeManageController::class, 'index']);
        Route::post('/service-types', [ServiceTypeManageController::class, 'store']);
        Route::put('/service-types/{id}', [ServiceTypeManageController::class, 'update']);
        Route::patch('/service-types/{id}/toggle', [ServiceTypeManageController::class, 'toggleStatus']);
        Route::delete('/service-types/{id}', [ServiceTypeManageController::class, 'destroy']);

        // Monitoring & Audit Log Aktivitas Sistem
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
        Route::get('/activity-logs/stats', [ActivityLogController::class, 'stats']);

        // Manajemen Pengguna Staf Admin
        Route::get('/users', [UserManageController::class, 'index']);
        Route::post('/users', [UserManageController::class, 'store']);
        Route::put('/users/{id}', [UserManageController::class, 'update']);
        Route::delete('/users/{id}', [UserManageController::class, 'destroy']);
    });
});
