<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use Illuminate\Support\Facades\Route;

// API V1 Routes
Route::prefix('v1')->name('api.v1.')->group(function (): void {
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function (): void {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');

        // Customer routes
        Route::apiResource('customers', CustomerController::class);
    });
});
