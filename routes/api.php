<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\SectionController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:password');

    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('sections', [SectionController::class, 'index']);

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

Route::middleware(['auth:sanctum', 'check.section:profiles'])->group(function () {
    Route::get('profiles/export/pdf', [ProfileController::class, 'exportPdf']);
    Route::get('profiles/export/excel', [ProfileController::class, 'exportExcel']);
    Route::apiResource('profiles', ProfileController::class);
});
