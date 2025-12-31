<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_article_and_it_is_pending()
    {
        // Create a user
        $user = User::factory()->create();

        // Login
        $this->actingAs($user, 'api');

        // Payload
        $payload = [
            'title' => 'Test Article Title',
            'content' => 'This is a test content that is definitely longer than 50 characters to pass validation requirements.',
            'category' => 'Cyber Security',
            'image_url' => 'https://example.com/image.jpg',
        ];

        // Send POST request
        $response = $this->postJson('/api/articles', $payload);

        // Assert status 201 Created
        $response->assertStatus(201);

        // Assert JSON structure
        $response->assertJsonStructure([
            'message',
            'article' => ['id', 'title', 'status']
        ]);

        // Assert Status is pending
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article Title',
            'status' => 'pending',
            'user_id' => $user->id
        ]);
    }

    public function test_validation_fails_for_short_content()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $payload = [
            'title' => 'Test',
            'content' => 'Short',
            'category' => 'Cyber Security',
            'image_url' => 'https://example.com/image.jpg',
        ];

        $response = $this->postJson('/api/articles', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']); // Validation is handled by Laravel
    }
}
