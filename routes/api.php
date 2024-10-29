<?php

use App\Http\Controllers\API\v1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) { */
/* return $request->user(); */
/* })->middleware('auth:sanctum'); */

/* Route::get('/', function (Request $request) { */
/* return response()->json(["message" => 'test']); */
/* })->middleware('throttle:api'); */

Route::middleware('throttle:api')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/per-hour', [TransactionController::class, 'get_count_per_hour']);
    Route::post('/cash', [TransactionController::class, 'cash']);
});
