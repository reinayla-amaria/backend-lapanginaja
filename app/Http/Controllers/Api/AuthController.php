<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'username' => 'required|string|min:3|unique:users|alpha_dash',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil!',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah akun sedang terkunci
        if ($user && $user->locked_until && now()->lt($user->locked_until)) {
            $menitSisa = now()->diffInMinutes($user->locked_until) + 1;
            return response()->json([
                'status'  => 'error',
                'message' => "Akun terkunci. Coba lagi dalam {$menitSisa} menit.",
            ], 429);
        }

        // Cek kredensial gagal
        if (!Auth::attempt($request->only('email', 'password'))) {
            if ($user) {
                $user->login_attempts += 1;

                if ($user->login_attempts >= 5) {
                    $user->locked_until = now()->addMinutes(15);
                    $user->login_attempts = 0;
                    $user->save();
                } else {
                    $user->save();
                }
            }

            \App\Models\LoginLog::create([
                'user_id'    => $user ? $user->id : null,
                'email'      => $request->email,
                'status'     => 'failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $sisaCobaan = $user ? (5 - $user->login_attempts) : null;
            return response()->json([
                'status'  => 'error',
                'message' => $sisaCobaan
                    ? "Email atau password salah. Sisa percobaan: {$sisaCobaan}."
                    : 'Email atau password salah.',
            ], 401);
        }

        // Login berhasil
        $user = Auth::user();
        $user->login_attempts = 0;
        $user->locked_until   = null;
        $user->save();

        \App\Models\LoginLog::create([
            'user_id'    => $user->id,
            'email'      => $request->email,
            'status'     => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => $user,
        ]);
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'name'      => 'required',
            'google_id' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email belum terdaftar. Silahkan register terlebih dahulu.',
            ], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => $user,
        ]);
    }
}