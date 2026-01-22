<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\WebhookHandleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
  Route::get('/', fn() => response()->json(['status' => 'ok']))
    ->name('api:status');

  if (!app()->environment('production')) {
    Route::post('/testing', [TestingController::class, 'demo'])
      ->name('testing');
  }
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
