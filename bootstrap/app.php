<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\InternalAuthHttpMiddleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    // web: __DIR__.'/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    channels: __DIR__ . '/../routes/channels.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    // add custom global middleware
    //   validate Internal-Auth header @/api/*
    $middleware->append(InternalAuthHttpMiddleware::class);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    // default error for api*
    $exceptions->render(function (NotFoundHttpException $error, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'error' => $error->getMessage(),
        ], 404);
      }
    });
  })->create();
