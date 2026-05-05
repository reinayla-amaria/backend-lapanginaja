<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lapangan;
use Illuminate\Support\Facades\Hash;

class GorIndramayuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dari file Word lu udah gue format ke Array PHP
        // Catatan: Harga gue ambil angka terendah dari range harganya biar masuk ke tipe data Integer
        $daftarGor = [
            ['nama' => 'Vianos Futsal and Badminton Club', 'jumlah' => 4, 'harga' => 40000, 'lokasi' => 'Jl. Veteran No.88, Lemahabang, Indramayu'],
            ['nama' => 'Bypass Badminton', 'jumlah' => 3, 'harga' => 40000, 'lokasi' => 'Jl. Bypass, Ujungaris, Widasari, Kabupaten Indramayu'],
            ['nama' => 'Gor Bulu Tangkis Bumi Patra', 'jumlah' => 2, 'harga' => 30000, 'lokasi' => 'Jl. Bumi Patra Raya No.62, Pekandangan, Kec. Indramayu'],
            ['nama' => 'GOR MM', 'jumlah' => 3, 'harga' => 30000, 'lokasi' => 'Jl. Perjuangan No.26, Kepandean, Indramayu'],
            ['nama' => 'Trisakti Badminton Arena', 'jumlah' => 1, 'harga' => 40000, 'lokasi' => 'Legok, Kec. Lohbener, Kabupaten Indramayu, Jawa Barat'],
            ['nama' => 'PB Pilang Badminton Court', 'jumlah' => 1, 'harga' => 30000, 'lokasi' => 'Desa Pilang, Kabupaten Indramayu'],
            ['nama' => 'Kelapa Gading Sport Arena', 'jumlah' => 6, 'harga' => 40000, 'lokasi' => 'Area Kelapa Gading, Indramayu'],
            ['nama' => 'PB. Tanjung', 'jumlah' => 1, 'harga' => 30000, 'lokasi' => 'Kedokan Bunder Wetan, Kec. Kedokan Bunder, Kabupaten Indramayu'],
            ['nama' => 'Gedung Bulutangkis Indramayu', 'jumlah' => 2, 'harga' => 35000, 'lokasi' => 'Tegalurung, Kec. Balongan, Kabupaten Indramayu'],
            ['nama' => 'Garuda Tangkas Indramayu', 'jumlah' => 1, 'harga' => 30000, 'lokasi' => 'Jl. Perjuangan RT/RW 01/02, Bojongsari, Kec. Indramayu'],
            ['nama' => 'HARINGGA SIRLA', 'jumlah' => 1, 'harga' => 30000, 'lokasi' => 'Desa Kebulen, Kecamatan Jatibarang, Kabupaten Indramayu'],
            ['nama' => 'Gelora Bulu Tangkis Ummat', 'jumlah' => 1, 'harga' => 30000, 'lokasi' => 'Jl. Siliwangi No.21, Jatibarang, Kecamatan Jatibarang, Kabupaten Indramayu'],
            ['nama' => 'Gor mini Jatibarang Bersama', 'jumlah' => 3, 'harga' => 40000, 'lokasi' => 'Jl. Mayor Dasuki, Jatibarang, Kecamatan Jatibarang, Kabupaten Indramayu'],
            ['nama' => 'Gor Badminton Krasak', 'jumlah' => 1, 'harga' => 30000, 'lokasi' => 'Ds. Krasak, Blok Gg. Kalen Maja, Jeruk, Kecamatan Jatibarang'],
        ];

        foreach ($daftarGor as $index => $gor) {
            $mitra = User::create([
                'name' => $gor['nama'],
                'email' => 'mitra' . ($index + 1) . '@lapanginaja.com',
                'password' => Hash::make('password'), // Password default buat semua mitra: password
                'role' => 'mitra',
            ]);

            // 2. Looping Bikin Lapangan Berdasarkan "Jumlah Lapangan"
            for ($i = 1; $i <= $gor['jumlah']; $i++) {
                Lapangan::create([
                    'mitra_id' => $mitra->id,
                    'nama_lapangan' => 'Lapangan ' . $i, // Bakal jadi: Lapangan 1, Lapangan 2, dst.
                    'lokasi' => $gor['lokasi'],
                    'harga_per_jam' => $gor['harga'],
                ]);
            }
        }
    }
}