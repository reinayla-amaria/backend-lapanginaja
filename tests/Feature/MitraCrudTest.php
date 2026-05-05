<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MitraCrudTest extends TestCase
{
    // Biar database testingnya di-reset setiap kali jalanin test
    use RefreshDatabase;

    // Fungsi bantuan buat bikin user Super Admin
    private function createAdmin()
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_halaman_kelola_mitra_tidak_bisa_diakses_tanpa_login()
    {
        $response = $this->get('/kelola-mitra');

        // Harusnya dilempar ke halaman login
        $response->assertRedirect('/login');
    }

    public function test_admin_bisa_melihat_halaman_kelola_mitra()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/kelola-mitra');

        $response->assertStatus(200);
        $response->assertSee('Kelola Mitra Arena');
    }

    public function test_admin_bisa_menambahkan_mitra_baru()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/kelola-mitra', [
            'name' => 'GOR Bulutangkis Baru',
            'email' => 'mitrabaru@lapanginaja.com',
            'password' => 'password123',
        ]);

        // Cek apakah dialihkan kembali ke halaman index
        $response->assertRedirect(route('mitra.index'));

        // Cek apakah data beneran masuk ke database
        $this->assertDatabaseHas('users', [
            'email' => 'mitrabaru@lapanginaja.com',
            'role' => 'mitra',
        ]);
    }

    public function test_admin_bisa_menghapus_data_mitra()
    {
        $admin = $this->createAdmin();
        $mitra = User::factory()->create(['role' => 'mitra']);

        $response = $this->actingAs($admin)->delete('/kelola-mitra/' . $mitra->id);

        $response->assertRedirect(route('mitra.index'));

        // Cek apakah data beneran hilang dari database
        $this->assertDatabaseMissing('users', [
            'id' => $mitra->id,
        ]);
    }
}