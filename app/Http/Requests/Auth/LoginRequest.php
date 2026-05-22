<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        // 1. Cek Lockout (Database)
        $user = User::where('email', $this->input('email'))->first();

        if ($user && $user->locked_until && now()->lessThan($user->locked_until)) {
            $minutes = now()->diffInMinutes($user->locked_until) + 1;
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
            ]);
        }

        // 2. Coba Login
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            
            // Jika Gagal, Update Attempts
            if ($user) {
                $user->increment('login_attempts');
                
                // Jika sudah 5 kali, kunci selama 10 menit
                if ($user->login_attempts >= 5) {
                    $user->update(['locked_until' => now()->addMinutes(10)]);
                }
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // 3. Jika Berhasil, Reset semuanya
        if ($user) {
            $user->update(['login_attempts' => 0, 'locked_until' => null]);
        }
    }
}