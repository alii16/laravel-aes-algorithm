<?php

use App\Http\Controllers\FileEncryptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FileEncryptionController::class, 'index']);
Route::post('/process', [FileEncryptionController::class, 'process'])->name('process');
