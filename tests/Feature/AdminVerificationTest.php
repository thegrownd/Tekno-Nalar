<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminVerificationTest extends TestCase
{
    // use RefreshDatabase; // Commented out to avoid wiping user's DB if they are using it locally without a separate test DB. 
    // Instead I will manually clean up or use transactions if possible, but for safety in this environment, I'll just create unique data.

    public function test_user_article_is_pending_and_visible_to_admin()
    {
        // 1. Create a regular user
        $user = User::factory()->create([
            'is_admin' => false,
            'is_active' => true, // Ensure active
        ]);

        // 2. User creates an article via API (to test the controller logic)
        $token = auth()->login($user);
        
        $articleData = [
            'title' => 'Test Article ' . uniqid(),
            'content' => 'This is a test article content that is long enough to pass validation.',
            'category' => 'Teknologi',
            'image_url' => 'https://example.com/image.jpg',
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/articles', $articleData);
        $response->assertStatus(201);
        
        $articleId = $response->json('article.id');
        $this->assertNotNull($articleId);

        // Verify status in DB
        $article = Article::find($articleId);
        $this->assertEquals('pending', $article->status);
        $this->assertFalse($article->is_admin_post);

        // 3. Create an admin user
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        // 4. Admin fetches pending articles
        $adminResponse = $this->actingAs($admin, 'api')->getJson('/api/admin/pending-articles');
        
        $adminResponse->assertStatus(200);
        
        // Assert the article is in the list
        $data = $adminResponse->json('data');
        $found = false;
        foreach ($data as $item) {
            if ($item['id'] === $articleId) {
                $found = true;
                break;
            }
        }
        
        $this->assertTrue($found, "Created article ID $articleId was not found in admin pending list");

        // Clean up
        $article->forceDelete();
        $user->delete();
        $admin->delete();
    }

    public function test_admin_can_filter_articles()
    {
        // 1. Create a regular user
        $user = User::factory()->create();

        // 2. Create articles with different statuses
        $pending = Article::factory()->create(['user_id' => $user->id, 'status' => 'pending', 'is_admin_post' => false]);
        $published = Article::factory()->create(['user_id' => $user->id, 'status' => 'published', 'is_admin_post' => false]);
        $rejected = Article::factory()->create(['user_id' => $user->id, 'status' => 'rejected', 'is_admin_post' => false]);

        // 3. Create admin
        $admin = User::factory()->create(['is_admin' => true]);

        // 4. Test filtering pending (default)
        $response = $this->actingAs($admin, 'api')->getJson('/api/admin/pending-articles?status=pending');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, collect($data)->where('id', $pending->id));
        $this->assertCount(0, collect($data)->where('id', $published->id));

        // 5. Test filtering published
        $response = $this->actingAs($admin, 'api')->getJson('/api/admin/pending-articles?status=published');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(0, collect($data)->where('id', $pending->id));
        $this->assertCount(1, collect($data)->where('id', $published->id));

        // 6. Test filtering all
        $response = $this->actingAs($admin, 'api')->getJson('/api/admin/pending-articles?status=all');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, collect($data)->where('id', $pending->id));
        $this->assertCount(1, collect($data)->where('id', $published->id));
        $this->assertCount(1, collect($data)->where('id', $rejected->id));

        // Cleanup
        $pending->forceDelete();
        $published->forceDelete();
        $rejected->forceDelete();
        $user->delete();
        $admin->delete();
    }
}
