<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'metode_pembayaran',
        'jumlah_bayar',
        'bukti_transfer',
        'status'
    ];

    // Relasi: Pembayaran ini buat bookingan yang mana?
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
