<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey());

        $user = \App\Models\User::where('email', $this->input('email'))->first();
        if ($user) {
            $user->increment('login_attempts');
            
            // Logika blokir 10 menit setelah 5 kali gagal
            if ($user->login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(10)]);
            }
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    // Reset jika berhasil
    $user = \App\Models\User::where('email', $this->input('email'))->first();
    if ($user) {
        $user->update(['login_attempts' => 0, 'locked_until' => null]);
    }

    RateLimiter::clear($this->throttleKey());
}
    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function ensureIsNotRateLimited(): void
{
    $user = \App\Models\User::where('email', $this->input('email'))->first();

    // Cek apakah user sedang diblokir
    if ($user && $user->locked_until && now()->lessThan($user->locked_until)) {
        $minutes = now()->diffInMinutes($user->locked_until) + 1;
        
        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
        ]);
    }
    // Tetap jalankan throttle bawaan Laravel
   if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
        return;
    }

    event(new Lockout($this));

    $seconds = RateLimiter::availableIn($this->throttleKey());
    throw ValidationException::withMessages([
        'email' => trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]),
    ]);
}

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
