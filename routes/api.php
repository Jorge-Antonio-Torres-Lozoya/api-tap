<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    // AuthController routes will be added in step 6
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Module routes will be added per step
});
