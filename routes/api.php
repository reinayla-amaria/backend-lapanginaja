<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\BookingController;


// URL: http://localhost:8000/api/lapangan
Route::get('/lapangan', [LapanganController::class, 'indexAPI']);
Route::post('/checkout', [BookingController::class, 'checkoutAPI']);
Route::post('/midtrans-callback', [BookingController::class, 'callbackAPI']);