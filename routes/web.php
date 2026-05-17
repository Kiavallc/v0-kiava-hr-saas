<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

// Authentication routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLogin'])->name('login');
        Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');

        Route::get('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendReset'])->name('send-reset');

        Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'showReset'])->name('reset');
        Route::post('/reset-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('reset.post');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/force-password-change', [App\Http\Controllers\Auth\ForcePasswordChangeController::class, 'show'])->name('force-password-change');
        Route::post('/force-password-change', [App\Http\Controllers\Auth\ForcePasswordChangeController::class, 'update'])->name('force-password-change.update');

        Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
