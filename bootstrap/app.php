<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_TOO_MANY_REQUESTS);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        });
    })
    ->create();
