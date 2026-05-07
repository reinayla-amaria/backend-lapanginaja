<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\Response;

class LapanganApiController extends Controller
{
    public function index()
    {
        // Tarik data lapangan, sekalian bawa data GOR-nya (relasi mitra)
        $lapangans = Lapangan::with('mitra')->latest()->get();

        // Balikin datanya dalam format JSON biar bisa dibaca Flutter
        return response()->json([
            'success' => true,
            'message' => 'Berhasil ngambil data lapangan',
            'data' => $lapangans
        ], 200);
    }
}
