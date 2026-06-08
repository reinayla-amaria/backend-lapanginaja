<?php

test('it returns a successful response', function () {
    $response = $this->get('/');

    // Ubah assertStatus(200) jadi assertRedirect
    $response->assertRedirect('/login');
});