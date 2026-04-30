<?php

test('Halaman Home Bisa Diakses', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Halaman Login Bisa Diakses', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Halaman Register Bisa Diakses', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('Halaman Mitra Bisa Diakses', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});


