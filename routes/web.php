<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LapanganController;
use App\Models\User;
use App\Models\Lapangan;
use App\Models\Booking;
use Carbon\Carbon;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Artisan;
// Halaman depan (Welcome)
Route::get('/', function () {
    return view('welcome');
});

Route::get('/jalanin-migrasi', function () {
    // Jalanin perintah migrate
    Artisan::call('migrate', ['--force' => true]);



    return 'Wih mantap, Migrasi Database Berhasil Bang!';
});
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        $data = [];

        if ($user->role === 'admin') {
            $data['total_mitra'] = User::where('role', 'mitra')->count();
            $data['total_penyewa'] = User::where('role', 'penyewa')->count(); // Asumsi role pengguna mobile = penyewa
            $data['total_transaksi'] = Booking::whereIn('status', ['sukses', 'dibayar'])->count();
            $data['mitra_baru'] = User::where('role', 'mitra')->latest()->take(5)->get(); // Ambil 5 mitra terbaru
        }
        // Kalau yang login MITRA, sedot data lapangannya sendiri
        elseif ($user->role === 'mitra') {
            $data['lapangan_aktif'] = Lapangan::where('mitra_id', $user->id)->count();

            $data['pesanan_pending'] = Booking::whereHas('lapangan', function ($q) use ($user) {
                $q->where('mitra_id', $user->id);
            })->where('status', 'pending')->count();

            $data['pesanan_hari_ini'] = Booking::whereHas('lapangan', function ($q) use ($user) {
                $q->where('mitra_id', $user->id);
            })->whereDate('tanggal_main', Carbon::today())->count();

            $data['riwayat_pesanan'] = Booking::with(['user', 'lapangan'])
                ->whereHas('lapangan', function ($q) use ($user) {
                    $q->where('mitra_id', $user->id);
                })->latest()->take(5)->get(); // Ambil 5 pesanan terbaru
        }

        return view('dashboard', $data);
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Route CRUD Lapangan (Khusus Mitra)
    Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
    Route::get('/lapangan/create', [LapanganController::class, 'create'])->name('lapangan.create');
    Route::post('/lapangan', [LapanganController::class, 'store'])->name('lapangan.store');

});

// Route Profil bawaan Laravel
// Route Profil bawaan Laravel
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/lapangan/{lapangan}', [LapanganController::class, 'destroy'])->name('lapangan.destroy');
    Route::get('/lapangan/{lapangan}/edit', [LapanganController::class, 'edit'])->name('lapangan.edit');
    Route::put('/lapangan/{lapangan}', [LapanganController::class, 'update'])->name('lapangan.update');

    // Rute Transaksi yang udah ada
    Route::get('/transaksi', [App\Http\Controllers\BookingController::class, 'indexMitra'])->name('transaksi.index');
    Route::get('/mitra', [App\Http\Controllers\MitraController::class, 'index'])->name('mitra.index');
    Route::get('/jadwal-lapangan', [App\Http\Controllers\BookingController::class, 'jadwal'])->name('mitra.jadwal');
    Route::post('/jadwal-lapangan/update', [App\Http\Controllers\BookingController::class, 'updateJadwal'])->name('mitra.jadwal.update');
    // TAMBAHIN RUTE INI BANG BIAR ROBOTNYA GAK 404 LAGI:
    Route::get('/transaksi/export', [App\Http\Controllers\BookingController::class, 'exportCSV'])->name('transaksi.export');
});
require __DIR__ . '/auth.php';

