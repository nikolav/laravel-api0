<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', function () {
        return response()->json(['status' => 'ok']);
    })->name('api:status');
});

Route::name('auth.')->prefix('auth')->group(function () {

    // unauthenticated
    Route::middleware(['guest'])->group(function () {
        // Public routes (no authentication required)
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
    });

    // protected, auth required
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/who', [AuthController::class, 'who'])->name('who');
    });
});
