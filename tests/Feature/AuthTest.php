<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_validates_and_creates_user(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'password' => 'Passw0rd!',
            'password_confirmation' => 'Passw0rd!',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', ['email' => 'tester@example.com']);
    }

    public function test_login_returns_jwt_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Passw0rd!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Passw0rd!',
        ]);

        $response->assertOk()->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);
    }
}
