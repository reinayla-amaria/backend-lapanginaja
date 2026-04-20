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

        $user = User::Create([
            'name' => $request->name,
            'email' =>$request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->sendEmailVerificationNotification();
        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil ! cek email untuk verifikasi',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'status' => 'eror',
                'message' => 'email atau password salah',
            ], 401);
        }
        $user = Auth::user();

        // Cek verifikasi email
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
        public function googleLogin(Request $request)
        {
            $request->validate([
                'email' => 'required|email',
                'name' => 'required',
                'google_id' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response ()->json([
                    'status' => 'eror',
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
    

