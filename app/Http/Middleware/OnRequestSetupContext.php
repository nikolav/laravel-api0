<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Context;

class OnRequestSetupContext
{
  /**
   * Handle an incoming request.
   *
   * @param  Closure(Request): (Response)  $next
   */
  function handle(Request $request, Closure $next): Response
  {
    Context::add([
      'foo' => 'bar',
    ]);

    return $next($request);
  }
}
