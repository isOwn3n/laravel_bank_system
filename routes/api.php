<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('transaction')->group(function () {
        Route::middleware(['throttle:transaction', 'is-transaction-limit-reached'])->group(function () {
            Route::post('/cash', [TransactionController::class, 'cash']);
            Route::post('/transfer', [TransactionController::class, 'transfer']);
        });
        Route::get('/balance', [TransactionController::class, 'getBalance']);
        Route::get('/top-users', [TransactionController::class, 'getThreeLastUsers']);
    });

    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
});
