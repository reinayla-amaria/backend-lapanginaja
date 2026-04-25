<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = User::where('role', 'mitra')
            ->withCount('lapangans')
            ->latest()
            ->get();

        return view('admin.mitra.index', compact('mitras'));
    }
}