<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiAdminTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
    }

    public function test_admin_bisa_melihat_halaman_pantau_transaksi()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/transaksi');

        $response->assertStatus(200);
    }

    public function test_fitur_export_csv_transaksi_berhasil_diunduh()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/transaksi/export');

        $response->assertStatus(200);
    }
}