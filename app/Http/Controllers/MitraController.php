<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Tambahin ini buat enkripsi password

class MitraController extends Controller
{
    // 1. READ: Tampil daftar mitra (Ini yang tadi lu bikin)
    public function index()
    {
        $mitras = User::where('role', 'mitra')
            ->withCount('lapangans')
            ->latest()
            ->get();

        return view('admin.mitra.index', compact('mitras'));
    }

    // 2. CREATE: Nampilin form tambah mitra
    public function create()
    {
        return view('admin.mitra.create');
    }

    // 3. STORE: Simpan data mitra baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mitra', // Set otomatis rolenya jadi mitra
        ]);

        return redirect()->route('mitra.index')->with('success', 'Data Mitra berhasil ditambahkan!');
    }

    // 4. EDIT: Nampilin form edit data mitra
    public function edit($id)
    {
        $mitra = User::findOrFail($id);
        return view('admin.mitra.edit', compact('mitra'));
    }

    // 5. UPDATE: Simpan perubahan data mitra
    public function update(Request $request, $id)
    {
        $mitra = User::findOrFail($id);

        // Validasi email diset biar bisa nge-save email yang sama buat id dia sendiri
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $mitra->id,
        ]);

        $mitra->name = $request->name;
        $mitra->email = $request->email;

        // Kalau field password diisi di form, baru kita update passwordnya
        if ($request->filled('password')) {
            $mitra->password = Hash::make($request->password);
        }

        $mitra->save();

        return redirect()->route('mitra.index')->with('success', 'Data Mitra berhasil diupdate!');
    }

    // 6. DELETE: Hapus data mitra
    public function destroy($id)
    {
        $mitra = User::findOrFail($id);
        $mitra->delete();

        return redirect()->route('mitra.index')->with('success', 'Data Mitra berhasil dihapus!');
    }
}