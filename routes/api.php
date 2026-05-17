<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\LapanganApiController;

// 1. Rute Lapangan (Hanya pakai satu saja)
Route::get('/lapangan', [LapanganApiController::class, 'index']);

// 2. Rute Booking & Midtrans
Route::post('/checkout', [BookingController::class, 'checkoutAPI']);
Route::post('/midtrans-callback', [BookingController::class, 'callbackAPI']);

// 3. Rute Auth Google
Route::post('/register_google', [AuthController::class, 'googleLogin']);