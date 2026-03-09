<?php

use App\Helpers\AppUtils;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn() => response()->json(AppUtils::res([
  'status' => 'ok'
], null), status: 200))->name('health');
