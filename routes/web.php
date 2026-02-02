<?php

use App\Http\Controllers\Auth\PopupOAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/oauth/{provider}/redirect', [PopupOAuthController::class, 'redirect'])
  ->name('oauth.redirect');

Route::get('/oauth/{provider}/callback', [PopupOAuthController::class, 'callback'])
  ->name('oauth.callback');
