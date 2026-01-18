<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function () {

    // Public routes (no authentication required)
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');

    // Protected routes (require authentication)
    Route::middleware(['auth:sanctum'])->group(function () {
        // Auth routes
        Route::get('/', function () {
            return ['status' => 'ok'];
        });

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/who', [AuthController::class, 'who'])->name('who');
    });
});
