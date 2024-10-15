<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SetApiAcceptHeaders;


Route::get('status', function (Request $request) {
    return response()->json(['status' => 'ok'], 200);
});

Route::middleware([SetApiAcceptHeaders::class])->group(function () {

    Route::get('user', [UserController::class, 'index'])->middleware('auth:sanctum');

    Route::post('login', [AuthController::class, 'login']);

    Route::post('register', [AuthController::class, 'register']);

    Route::prefix('expenses')->middleware('auth:sanctum')->group(function () {
        Route::post('/', [ExpenseController::class, 'index']);
        Route::get('/{id}', [ExpenseController::class, 'getExpenseById']);
        Route::post('/image', [ExpenseController::class, 'createExpenseFromImage']);
        Route::post('/image/{id}', [ExpenseController::class, 'attachImageToId']);
    });
});

