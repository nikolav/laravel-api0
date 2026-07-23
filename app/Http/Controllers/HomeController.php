<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Aws\Sdk as AwsSdk;

use App\Helpers\AppUtils;

class HomeController extends Controller
{
  /**
   * Handle the incoming request.
   */
  function __invoke(Request $request)
  {
    return response()->json(AppUtils::res([
      'app_name' => config('app.name'),
      'aws-sdk'  => AwsSdk::VERSION,
    ], null));
  }
}
