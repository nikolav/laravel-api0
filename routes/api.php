<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\WebhookHandleController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', function () {
        return response()->json([
            'status'        => 'ok',
            'internal-auth' => request()->header('Internal-Auth'),
        ]);
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

Route::any('/webhooks/{key?}', [WebhookHandleController::class, 'webhook'])->name('webhooks');
Route::get('/health', fn() => response()->json(['status' => 'ok'], 200))->name('healthcheck');
