<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\DonationRequestController;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Protected ────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',         [AuthController::class, 'logout']);
    Route::get('/me',              [AuthController::class, 'me']);
    Route::put('/profile',         [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Donations
    Route::get('/donations',          [DonationController::class, 'index']);
    Route::get('/my-donations',       [DonationController::class, 'myDonations']);
    Route::post('/donations',         [DonationController::class, 'store']);
    Route::delete('/donations/{id}',  [DonationController::class, 'destroy']);

    // Requests
    Route::post('/donations/{id}/request',
        [DonationRequestController::class, 'store']);
    Route::get('/my-requests',
        [DonationRequestController::class, 'myRequests']);
    Route::patch('/donation-requests/{id}/status',
        [DonationRequestController::class, 'updateStatus']);
    Route::patch('/donation-requests/{id}/collect',
        [DonationRequestController::class, 'confirmCollection']);

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/stats',               [AdminController::class, 'stats']);
        Route::get('/users',               [AdminController::class, 'users']);
        Route::patch('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
        Route::get('/donations',           [AdminController::class, 'donations']);
    });
});