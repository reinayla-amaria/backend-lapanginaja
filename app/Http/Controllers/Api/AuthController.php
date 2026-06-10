<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    $otp = rand(100000, 999999);

    $user = User::create([
        'name'        => $request->name,
        'username'    => $request->username,
        'email'       => $request->email,
        'password'    => Hash::make($request->password),
        'otp_code'    => $otp,
        'otp_expires_at' => now()->addMinutes(10),
        'is_verified' => false,
    ]);

    // Kirim OTP ke email
    Mail::raw("Kode OTP LapanginAja kamu: $otp\nBerlaku 10 menit.", function ($message) use ($user) {
        $message->to($user->email)->subject('Verifikasi OTP LapanginAja');
    });

    return response()->json([
        'status'  => 'success',
        'message' => 'OTP telah dikirim ke email kamu.',
        'email'   => $user->email,
    ]);
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp'   => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan.'], 404);
    }

    if ($user->is_verified) {
        return response()->json(['status' => 'error', 'message' => 'Akun sudah terverifikasi.'], 400);
    }

    if ($user->otp_code !== $request->otp) {
        return response()->json(['status' => 'error', 'message' => 'OTP salah.'], 400);
    }

    if (now()->gt($user->otp_expires_at)) {
        return response()->json(['status' => 'error', 'message' => 'OTP sudah kadaluarsa.'], 400);
    }

    $user->update([
        'is_verified'    => true,
        'otp_code'       => null,
        'otp_expires_at' => null,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status'  => 'success',
        'message' => 'Akun berhasil diverifikasi!',
        'token'   => $token,
        'user'    => $user,
    ]);
}

public function resendOtp(Request $request)
{
    $request->validate(['email' => 'required|email']);
    $user = User::where('email', $request->email)->first();

    if (!$user || $user->is_verified) {
        return response()->json(['status' => 'error', 'message' => 'Tidak valid.'], 400);
    }

    $otp = rand(100000, 999999);
    $user->update(['otp_code' => $otp, 'otp_expires_at' => now()->addMinutes(10)]);

    Mail::raw("Kode OTP LapanginAja kamu: $otp\nBerlaku 10 menit.", function ($message) use ($user) {
        $message->to($user->email)->subject('Verifikasi OTP LapanginAja');
    });

    return response()->json(['status' => 'success', 'message' => 'OTP baru telah dikirim.']);
}

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && !$user->is_verified) {
    return response()->json([
        'status'  => 'error',
        'message' => 'Akun belum diverifikasi. Cek email kamu.',
    ], 403);
}
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