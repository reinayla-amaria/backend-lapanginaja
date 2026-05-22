<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function () {
            throw new \Exception('Log tidak boleh dimodifikasi.');
        });

        static::deleting(function ($log) {
            if ($log->created_at->diffInDays(now()) < 90) {
                throw new \Exception('Log tidak boleh dihapus sebelum 90 hari.');
            }
        });
    }
}