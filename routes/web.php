<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login.store');

Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register.store');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::middleware('auth')->group(function () {
    // Force password change if needed
    Route::middleware('force.password.change')->group(function () {
        Route::get('/force-password-change', [\App\Http\Controllers\Auth\PasswordController::class, 'showForceChangeForm'])->name('password.force-change');
        Route::post('/force-password-change', [\App\Http\Controllers\Auth\PasswordController::class, 'updateForcedPassword'])->name('password.update-forced');
    });

    // Main dashboard routes
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Employee routes
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);

    // Document routes
    Route::resource('documents', \App\Http\Controllers\DocumentController::class);
    Route::post('/documents/{document}/approve', [\App\Http\Controllers\DocumentController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [\App\Http\Controllers\DocumentController::class, 'reject'])->name('documents.reject');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');

    // Notification routes
    Route::resource('notifications', \App\Http\Controllers\NotificationController::class, ['only' => ['index']]);
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
        Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class, ['only' => ['index', 'show']]);
        Route::post('/documents/{document}/approve', [\App\Http\Controllers\Admin\DocumentController::class, 'approve'])->name('documents.approve');
        Route::post('/documents/{document}/reject', [\App\Http\Controllers\Admin\DocumentController::class, 'reject'])->name('documents.reject');
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    });
});
