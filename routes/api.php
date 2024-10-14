<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;


Route::get('status', function (Request $request) {
    return response()->json(['status' => 'ok'], 200);
});

Route::get('user', [UserController::class, 'index'])->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);

Route::post('register', [AuthController::class, 'register']);

Route::prefix('expenses')->group(function () {
    Route::post('/', [ExpenseController::class, 'index'])->middleware('auth:sanctum');
});
