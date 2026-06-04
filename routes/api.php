<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'check.section:products'])->group(function () {
    Route::get('products/export/pdf', [ProductController::class, 'exportPdf']);
    Route::get('products/export/excel', [ProductController::class, 'exportExcel']);
    Route::apiResource('products', ProductController::class);
});

Route::middleware(['auth:sanctum', 'check.section:users'])->group(function () {
    Route::get('users/export/pdf', [UserController::class, 'exportPdf']);
    Route::get('users/export/excel', [UserController::class, 'exportExcel']);
    Route::apiResource('users', UserController::class);
});
