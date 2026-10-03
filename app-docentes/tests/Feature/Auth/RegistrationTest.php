<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_registro_publico_esta_deshabilitado(): void
    {
        $response = $this->get('/register');
        $response->assertNotFound();

        $postResponse = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $postResponse->assertNotFound();
        $this->assertGuest();
    }
}
