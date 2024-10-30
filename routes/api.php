<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

/*Route::group([
    'prefix' => ''
], function () {
     Route::get('/', [TransactionController::class, 'index']);
     Route::get('/per-hour', [TransactionController::class, 'get_count_per_hour']);
    }); */
/* ->middleware('throttle:api') */

Route::post('/cash', [TransactionController::class, 'cash'])->middleware('auth:sanctum');
Route::get('/redis', [TransactionController::class, 'testRedis'])->middleware('auth:sanctum');
Route::get('/last', [TransactionController::class, 'get_three_last_users'])->middleware('auth:sanctum');
