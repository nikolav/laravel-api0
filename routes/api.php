<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\WebhookHandleController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GraphqlController;
use Aws\Sdk as AwsSdk;

Route::middleware(['auth:sanctum'])->group(function () {
  Route::get(
    '/',
    fn() => response()->json([
      'status'  => 'ok',
      'aws-sdk' => AwsSdk::VERSION,
    ])
  )->name('api:status');

  Route::post('/graphql', GraphqlController::class);

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

// mount broadcast auth under /api/broadcasting/auth
//  clients point to [POST /api/broadcasting/auth], (echo)
Broadcast::routes([
  // 'prefix' => 'broadcasting',
  'middleware' => ['auth:sanctum'],
]);
