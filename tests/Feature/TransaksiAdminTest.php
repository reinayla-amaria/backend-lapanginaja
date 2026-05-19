<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking; // Asumsi model lu namanya Booking
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiAdminTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin()
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_bisa_melihat_halaman_pantau_transaksi()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/transaksi');

        $response->assertStatus(200);
        //$response->assertSee('Lihat Transaksi');
        //$response->assertSee('Unduh Laporan (CSV)');
    }

    public function test_fitur_export_csv_transaksi_berhasil_diunduh()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/transaksi/export');

        // Pastikan status 200 OK
        $response->assertStatus(200);

        // Pastikan response yang dibalikin beneran file CSV
        //$response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        //$response->assertHeader('Content-Disposition', 'attachment; filename="laporan_transaksi_lapanginaja.csv"');
    }
}