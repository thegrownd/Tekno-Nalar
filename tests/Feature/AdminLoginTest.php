<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_correct_credentials()
    {
        // Create admin user
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Nagasaya1'),
            'is_admin' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@gmail.com',
            'password' => 'Nagasaya1',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'user' => ['id', 'email', 'is_admin']
            ]);
    }

    public function test_admin_cannot_login_with_wrong_password_case()
    {
        // Create admin user
        User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Nagasaya1'),
            'is_admin' => true,
        ]);

        // Wrong case password
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@gmail.com',
            'password' => 'nagasaya1', // lowercase n
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_cannot_login_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Nagasaya1'),
            'is_admin' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@gmail.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401);
    }
}
