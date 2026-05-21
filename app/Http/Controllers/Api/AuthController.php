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
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([ 
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // $user->sendEmailVerificationNotification(); // Dimatikan sementara

        // Langsung kasih token biar Flutter bisa auto-login
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil!',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    // Cek apakah akun sedang terkunci
    if ($user && $user->locked_until && now()->lt($user->locked_until)) {
        $menitSisa = now()->diffInMinutes($user->locked_until) + 1;
        return response()->json([
            'status' => 'error',
            'message' => "Akun terkunci. Coba lagi dalam {$menitSisa} menit.",
        ], 429);
    }

    // Cek kredensial
    if (!Auth::attempt($request->only('email', 'password'))) {
        if ($user) {
            $user->login_attempts += 1;

            if ($user->login_attempts >= 5) {
                $user->locked_until = now()->addMinutes(10);
                $user->login_attempts = 0;
                $user->save();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun terkunci selama 10 menit karena 5 kali percobaan login gagal.',
                ], 429);
            }

            $sisaCobaan = 5 - $user->login_attempts;
            $user->save();

            return response()->json([
                'status' => 'error',
                'message' => "Email atau password salah. Sisa percobaan: {$sisaCobaan}.",
            ], 401);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau password salah.',
        ], 401);
    }

    // Login berhasil — reset counter
    $user = Auth::user();
    $user->login_attempts = 0;
    $user->locked_until = null;
    $user->save();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'token' => $token,
        'user' => $user,
    ]);
}
    public function googleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'google_id' => 'required',
        ]);

        $user = User::where('email', $request->email)->first(); // Perbaikan tanda $

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'email belum terdaftar. silahkan register terlebih dahulu',
            ], 404);
        }

        
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email belum diverifikasi.',
            ], 403);
        }
        

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user,
        ]);
    }
}