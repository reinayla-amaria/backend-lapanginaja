<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    protected $fillable = [
        'mitra_id',
        'nama_lapangan',
        'lokasi',
        'harga_per_jam',
        'foto'
    ];

    // Relasi: Lapangan ini punyanya siapa? (Punya User yang role-nya Mitra)
    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    // Relasi: 1 Lapangan bisa dibooking berkali-kali
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
