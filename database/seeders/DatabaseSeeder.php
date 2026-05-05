<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bikin 1 Akun Khusus Admin Abadi
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@lapanginaja.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->call([
            GorIndramayuSeeder::class,
        ]);
    }
}