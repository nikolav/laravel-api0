<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalAuthHttpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (
            empty(config('app.internal-auth')) ||
            empty($request->header('Internal-Auth')) ||
            (config('app.internal-auth') !== $request->header('Internal-Auth'))
        ) {
            // @invalid:abort Internal-Auth
            return abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
