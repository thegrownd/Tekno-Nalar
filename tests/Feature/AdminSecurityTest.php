<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_routes()
    {
        $response = $this->getJson('/api/admin/users');
        
        $response->assertStatus(403);
        
        // Verify log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'unauthorized_access',
            'description' => 'Unauthorized access attempt by guest to api/admin/users'
        ]);
    }

    public function test_regular_user_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['is_admin' => false]);
        
        $response = $this->actingAs($user, 'api')->getJson('/api/admin/users');
        
        $response->assertStatus(403);
        
        // Verify log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'unauthorized_access',
            'user_id' => $user->id,
            'description' => "Unauthorized access attempt by user {$user->name} ({$user->email}) to api/admin/users"
        ]);
    }

    public function test_admin_can_access_admin_routes()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $response = $this->actingAs($admin, 'api')->getJson('/api/admin/users');
        
        $response->assertStatus(200);
        
        // Should NOT log unauthorized access
        $this->assertDatabaseMissing('activity_logs', [
            'action' => 'unauthorized_access',
            'user_id' => $admin->id
        ]);
    }

    public function test_admin_can_access_pending_articles()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $response = $this->actingAs($admin, 'api')->getJson('/api/admin/pending-articles');
        
        $response->assertStatus(200);
    }
}
