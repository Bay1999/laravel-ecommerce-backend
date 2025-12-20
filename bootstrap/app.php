<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    //
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (\Throwable $e, $request) {
      if ($request->is('api/*')) {

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
          $model = class_basename($e->getModel());

          return response()->json([
            'status' => 'error',
            'message' => "$model not found",
            'code' => 'RESOURCE_NOT_FOUND'
          ], 404);
        }

        // 2. URL/Route Not Found
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
          return response()->json([
            'status' => 'error',
            'message' => 'The requested endpoint does not exist'
          ], 404);
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
          return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $e->errors()
          ], 422);
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
          return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        return response()->json([
          'status' => 'error',
          'message' => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
          'type' => class_basename($e)
        ], 500);
      }
    });
  })->create();
