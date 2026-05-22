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
        // Cari user berdasarkan email
        $user = User::where('email', $this->input('email'))->first();

        // Cek apakah akun sedang dikunci
        if ($user && $user->locked_until && now()->lessThan($user->locked_until)) {

            $minutes = now()->diffInMinutes($user->locked_until) + 1;

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
            ]);
        }

        // Coba login
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {

            // Jika gagal login
            if ($user) {
                $user->increment('login_attempts');
                
                $user->refresh();

                // Jika sudah 5x gagal
                if ($user->login_attempts >= 5) {

                    $user->update([
                        'locked_until' => now()->addMinutes(10),
                    ]);

                    throw ValidationException::withMessages([
                        'email' => 'Akun dikunci selama 10 menit karena terlalu banyak percobaan login.',
                    ]);
                }
            }

            // Error login biasa
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        if ($user) {
            $user->update([
                'login_attempts' => 0,
                'locked_until' => null,
            ]);
        }
    }
}