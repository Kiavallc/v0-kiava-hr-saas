<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        // Authentication
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);

        // Employees
        Route::apiResource('employees', \App\Http\Controllers\Api\EmployeeController::class);

        // Documents
        Route::apiResource('documents', \App\Http\Controllers\Api\DocumentController::class);
        Route::post('/documents/{document}/approve', [\App\Http\Controllers\Api\DocumentController::class, 'approve']);
        Route::post('/documents/{document}/reject', [\App\Http\Controllers\Api\DocumentController::class, 'reject']);
        Route::get('/documents/{document}/download-url', [\App\Http\Controllers\Api\DocumentController::class, 'getDownloadUrl']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);

        // Dashboard metrics
        Route::get('/dashboard/metrics', [\App\Http\Controllers\Api\DashboardController::class, 'metrics']);

        // Audit logs
        Route::get('/audit-logs', [\App\Http\Controllers\Api\AuditLogController::class, 'index']);
    });
});

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/forgot-password', [\App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
});
