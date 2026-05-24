<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\LapanganApiController;

// Public - dengan rate limiting
Route::middleware('throttle:public')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register_google', [AuthController::class, 'googleLogin']);
    Route::get('/lapangan', [LapanganApiController::class, 'index']);
    Route::post('/midtrans-callback', [BookingController::class, 'callbackAPI']);
});

// Hanya penyewa yang bisa akses
Route::middleware(['auth:sanctum', 'role:penyewa'])->group(function () {
    Route::post('/checkout', [BookingController::class, 'checkoutAPI']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
});