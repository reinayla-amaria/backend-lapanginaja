<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'no_hp',
        'login_attempts',  
        'locked_until', 
    ];
    protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'locked_until' => 'datetime', 
    'no_hp' => 'encrypted',   
    'name' => 'encrypted',
];

    public function lapangans()
    {
        // Harus sebutin 'mitra_id' karena kita gak pake default 'user_id'
        return $this->hasMany(Lapangan::class, 'mitra_id');
    }

    // Relasi kalau user ini adalah Penyewa (1 Penyewa punya banyak Booking)
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'penyewa_id');
    }
public function twoFactorAuth()
{
    return $this->hasOne(\App\Models\TwoFactorAuthentication::class);
}
    
}