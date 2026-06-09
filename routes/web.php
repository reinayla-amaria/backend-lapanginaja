<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

use App\Http\Controllers\LapanganController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use App\Models\Lapangan;
use App\Models\Booking;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/jalanin-migrasi', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Wih mantap, Migrasi Database Berhasil Bang!';
});

//admmin
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {

    Route::get('/transaksi', [BookingController::class, 'index'])
        ->name('admin.transaksi.index');

    Route::get('/transaksi/export', [BookingController::class, 'exportCSV'])
        ->name('transaksi.export');
    // Tambahkan di dalam route middleware admin
    Route::get('/transaksi/{id}/edit', [BookingController::class, 'edit'])->name('admin.transaksi.edit');
    Route::put('/transaksi/{id}', [BookingController::class, 'update'])->name('admin.transaksi.update');

    // KELOLA MITRA
    Route::resource('kelola-mitra', MitraController::class)->names('mitra');

    // LOGS
    Route::get('/login-logs', function () {
        $logs = \App\Models\LoginLog::latest()->paginate(50);
        return response()->json($logs);
    });
});

//mitra
Route::middleware(['auth', 'verified', 'role:mitra'])->group(function () {

    Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
    Route::get('/lapangan/create', [LapanganController::class, 'create'])->name('lapangan.create');
    Route::post('/lapangan', [LapanganController::class, 'store'])->name('lapangan.store');
    Route::get('/lapangan/{lapangan}/edit', [LapanganController::class, 'edit'])->name('lapangan.edit');
    Route::put('/lapangan/{lapangan}', [LapanganController::class, 'update'])->name('lapangan.update');
    Route::delete('/lapangan/{lapangan}', [LapanganController::class, 'destroy'])->name('lapangan.destroy');

    Route::get('/jadwal-lapangan', [BookingController::class, 'jadwal'])->name('mitra.jadwal');
    Route::post('/jadwal-lapangan/update', [BookingController::class, 'updateJadwal'])->name('mitra.jadwal.update');

    Route::get('/mitra/chat', [ChatController::class, 'index'])->name('mitra.chat');
});

//dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/mfa', [ProfileController::class, 'disableMfa'])->name('profile.mfa.disable');
});

require __DIR__ . '/auth.php';