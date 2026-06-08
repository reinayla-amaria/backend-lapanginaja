<?php

test('Halaman Home melempar ke Login', function () {
    $response = $this->get('/');

    // Karena di web.php kita setting redirect, maka expect-nya redirect
    $response->assertRedirect('/login');
});

test('Halaman Login Bisa Diakses', function () {
    // Ubah URL-nya jadi /login
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('Halaman Register Bisa Diakses', function () {
    // Ubah URL-nya jadi /register
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('Halaman Mitra Butuh Login', function () {
    // Akses rute mitra, misalnya /kelola-mitra
    $response = $this->get('/kelola-mitra');

    // Karena belum login, otomatis dilempar ke halaman login (302)
    $response->assertRedirect('/login');
});