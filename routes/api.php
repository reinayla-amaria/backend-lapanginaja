<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LapanganApiController;



// URL: http://localhost:8000/api/lapangan
Route::get('/lapangan', [LapanganController::class, 'indexAPI']);
Route::post('/checkout', [BookingController::class, 'checkoutAPI']);
Route::post('/midtrans-callback', [BookingController::class, 'callbackAPI']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::get('/lapangan', [LapanganApiController::class, 'index']);