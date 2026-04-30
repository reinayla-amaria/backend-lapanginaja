<?php

use App\Http\Controllers\MitraController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\VulnerableUserController;
use App\Models\User;
use App\Models\Lapangan;
use App\Models\Booking;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        $data = [];

        if ($user->role === 'penyewa') {
            abort(403, 'Woy bang! Lu cuma penyewa, dilarang masuk ke ruang admin!');
        }

        if ($user->role === 'admin') {
            $data['total_mitra'] = User::where('role', 'mitra')->count();
            $data['total_penyewa'] = User::where('role', 'penyewa')->count(); // Asumsi role pengguna mobile = penyewa
            $data['total_transaksi'] = Booking::whereIn('status', ['sukses', 'dibayar'])->count();
            $data['mitra_baru'] = User::where('role', 'mitra')->latest()->take(5)->get(); // Ambil 5 mitra terbaru
        } elseif ($user->role === 'mitra') {
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
    })->name('dashboard');

    // Route Lapangan
    Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
    Route::get('/lapangan/create', [LapanganController::class, 'create'])->name('lapangan.create');
    Route::post('/lapangan', [LapanganController::class, 'store'])->name('lapangan.store');

});

Route::middleware('auth')->group(function () {
    // Route Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Lapangan (Lanjutan)
    Route::delete('/lapangan/{lapangan}', [LapanganController::class, 'destroy'])->name('lapangan.destroy');
    Route::get('/lapangan/{lapangan}/edit', [LapanganController::class, 'edit'])->name('lapangan.edit');
    Route::put('/lapangan/{lapangan}', [LapanganController::class, 'update'])->name('lapangan.update');

    // Route Transaksi
    Route::get('/transaksi', [App\Http\Controllers\BookingController::class, 'indexMitra'])->name('transaksi.index');

    // === INI ROUTE CRUD KELOLA MITRA BARU NYA BANG ===
    Route::get('/kelola-mitra', [MitraController::class, 'index'])->name('mitra.index');
    Route::get('/kelola-mitra/create', [MitraController::class, 'create'])->name('mitra.create');
    Route::post('/kelola-mitra', [MitraController::class, 'store'])->name('mitra.store');
    Route::get('/kelola-mitra/{id}/edit', [MitraController::class, 'edit'])->name('mitra.edit');
    Route::put('/kelola-mitra/{id}', [MitraController::class, 'update'])->name('mitra.update');
    Route::delete('/kelola-mitra/{id}', [MitraController::class, 'destroy'])->name('mitra.destroy');
});

Route::get('/vulnerable/search', [VulnerableUserController::class, 'searchVulnerable']);

require __DIR__ . '/auth.php';