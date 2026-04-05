<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    public function indexAPI()
    {
        $lapangans = Lapangan::with('mitra')->get();

        return response()->json([
            'status' => 'sukses',
            'pesan' => 'Berhasil mengambil daftar lapangan',
            'data' => $lapangans
        ]);
    }
    public function index()
    {
        $lapangans = Lapangan::where('mitra_id', Auth::id())->get();

        return view('lapangan.index', compact('lapangans'));
    }
    public function create()
    {
        return view('lapangan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lapangan' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'harga_per_jam' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('lapangan', 'public');
        }

        Lapangan::create([
            'mitra_id' => Auth::id(),
            'nama_lapangan' => $request->nama_lapangan,
            'lokasi' => $request->lokasi,
            'harga_per_jam' => $request->harga_per_jam,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('lapangan.index')->with('success', 'Mantap! Lapangan baru berhasil ditambahkan.');
    }
    public function destroy(Lapangan $lapangan)
    {
        if ($lapangan->foto) {
            Storage::disk('public')->delete($lapangan->foto);
        }

        $lapangan->delete();

        return redirect()->route('lapangan.index')->with('success', 'Data lapangan berhasil dihapus!');
    }
    public function edit(Lapangan $lapangan)
    {
        return view('lapangan.edit', compact('lapangan'));
    }

    public function update(Request $request, Lapangan $lapangan)
    {
        $request->validate([
            'nama_lapangan' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'harga_per_jam' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto yang lama (kalau ada)
            if ($lapangan->foto) {
                Storage::disk('public')->delete($lapangan->foto);
            }
            $lapangan->foto = $request->file('foto')->store('lapangan', 'public');
        }

        $lapangan->update([
            'nama_lapangan' => $request->nama_lapangan,
            'lokasi' => $request->lokasi,
            'harga_per_jam' => $request->harga_per_jam,
            'foto' => $lapangan->foto,
        ]);

        return redirect()->route('lapangan.index')->with('success', 'Data lapangan berhasil diupdate!');
    }
}