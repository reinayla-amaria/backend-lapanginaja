<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Lapangan;

class DashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();

    if ($user->role === 'admin') {
        return view('dashboard', [
            'total_mitra' => User::where('role', 'mitra')->count(),
            'total_penyewa' => User::where('role', 'penyewa')->count(),
            'total_transaksi' => Booking::whereIn('status', ['sukses','dibayar'])->count(),
            'mitra_baru' => User::where('role', 'mitra')->latest()->take(5)->get(),

            // dummy biar blade aman
            'lapangan_aktif' => 0,
            'pesanan_pending' => 0,
            'pesanan_hari_ini' => 0,
            'riwayat_pesanan' => collect(),
        ]);
    }

    $riwayat = Booking::with(['user', 'lapangan'])
        ->whereHas('lapangan', function ($q) use ($user) {
            $q->where('mitra_id', $user->id);
        })
        ->latest()
        ->take(10)
        ->get();

    return view('dashboard', [
        'lapangan_aktif' => Lapangan::where('mitra_id', $user->id)->count(),

        'pesanan_pending' => Booking::whereHas('lapangan', function ($q) use ($user) {
            $q->where('mitra_id', $user->id);
        })->where('status', 'pending')->count(),

        'pesanan_hari_ini' => Booking::whereHas('lapangan', function ($q) use ($user) {
            $q->where('mitra_id', $user->id);
        })->whereDate('created_at', today())->count(),

        'riwayat_pesanan' => $riwayat,
    ]);
}
}