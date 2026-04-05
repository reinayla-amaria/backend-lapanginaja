<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'lapangan_id',
        'tanggal_main',
        'jam_mulai',
        'jam_selesai',
        'total_harga',
        'status'
    ];

    // Relasi: Bookingan ini punyanya siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Bookingan ini mesen lapangan yang mana?
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }

    // Relasi: 1 Bookingan cuma punya 1 Pembayaran (One-to-One)
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
