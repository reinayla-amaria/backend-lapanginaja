<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VulnerableUserController extends Controller
{
    // VULNERABLE: SQL Injection
  public function searchVulnerable(Request $request)
{
    $search = $request->input('search');

    $users = DB::table('users')
        ->where('name', 'like', "%{$search}%")
        ->get();

    return view('users.search', compact('users'));
}
}