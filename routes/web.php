<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileEncryptionController;

// File Encryption Routes
Route::get('/', [FileEncryptionController::class, 'index'])->name('home');
Route::post('/process', [FileEncryptionController::class, 'process'])->name('process');
Route::get('/download/{token}', [FileEncryptionController::class, 'download'])->name('download');

// Alternative routes for better SEO and user experience
Route::get('/encrypt', [FileEncryptionController::class, 'index'])->name('encrypt');
Route::get('/decrypt', [FileEncryptionController::class, 'index'])->name('decrypt');

// Health check route (optional)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'SecureFile Encryption Service',
        'timestamp' => now()->toISOString()
    ]);
})->name('health');