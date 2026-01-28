<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\WebhookHandleController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATED API CORE
|--------------------------------------------------------------------------
| Protected API endpoints.
| - Require Sanctum authentication
| - Represent the "logged-in" surface of the API
| - Safe to expose internal metadata here
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

  /*
    |----------------------------------------------------------------------
    | API Status / Health (authenticated)
    |----------------------------------------------------------------------
    | Lightweight sanity check for:
    | - auth wiring
    | - API availability
    |----------------------------------------------------------------------
    */
  Route::get('/', fn() => response()->json([
    'status'  => 'ok',
  ]))->name('api.status');

  /*
    |----------------------------------------------------------------------
    | Development / Testing Utilities
    |----------------------------------------------------------------------
    | Non-production helpers.
    | - Explicitly disabled in production
    |----------------------------------------------------------------------
    */
  if (!app()->environment('production')) {
    Route::post('/testing', [TestingController::class, 'demo'])
      ->name('testing.demo');
  }
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION API
|--------------------------------------------------------------------------
| Authentication lifecycle endpoints.
| Split clearly into:
| - guest-only (login / register)
| - authenticated (logout / whoami)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->group(function () {

  /*
    |----------------------------------------------------------------------
    | Public Auth Endpoints (Guest Only)
    |----------------------------------------------------------------------
    | - No authentication allowed
    | - Used for initial access
    |----------------------------------------------------------------------
    */
  Route::middleware(['guest', 'throttle:api'])->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
      ->name('register');

    Route::post('/authenticate', [AuthController::class, 'authenticate'])
      ->name('authenticate');
  });

  /*
    |----------------------------------------------------------------------
    | Protected Auth Endpoints
    |----------------------------------------------------------------------
    | - Require valid Sanctum token
    | - Session / identity management
    |----------------------------------------------------------------------
    */
  Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
      ->name('logout');

    Route::get('/who', [AuthController::class, 'who'])
      ->name('who');
  });
});

/*
|--------------------------------------------------------------------------
| EXTERNAL / PUBLIC INTEGRATIONS
|--------------------------------------------------------------------------
| Endpoints intended for external systems.
| - Do NOT require Sanctum auth
| - Security handled internally (keys, signatures, etc.)
|--------------------------------------------------------------------------
*/
Route::any('/webhooks/{key?}', [WebhookHandleController::class, 'webhook'])
  ->name('webhooks.handle');

/*
|--------------------------------------------------------------------------
| PUBLIC HEALTH CHECK
|--------------------------------------------------------------------------
| Infrastructure-level health probe.
| - Used by load balancers / uptime monitors
| - No auth, no side effects
|--------------------------------------------------------------------------
*/
Route::get('/health', fn() => response()->json(['status' => 'ok'], 200))
  ->name('healthcheck');

/*
|--------------------------------------------------------------------------
| LARAVEL BROADCASTING AUTH
|--------------------------------------------------------------------------
| Pusher / Reverb / Echo authentication endpoint.
| - Mounted under /api/broadcasting/auth
| - Clients MUST POST here
| - Protected by Sanctum (token-based auth)
|--------------------------------------------------------------------------
*/
Broadcast::routes([
  'middleware' => ['auth:sanctum'],
  // 'prefix' => 'broadcasting', // default, intentionally left explicit
]);
