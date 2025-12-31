<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\ActivityLog;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_articles_public(): void
    {
        Article::factory()->count(3)->create();
        $response = $this->getJson('/api/articles');
        $response->assertOk()->assertJsonStructure(['data']);
    }

    public function test_non_admin_cannot_delete_article(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $create = $this->postJson('/api/articles', [
            'title' => 'Judul',
            'content' => 'Konten',
            'category' => 'Teknologi',
            'image_url' => 'https://picsum.photos/seed/test-create/800/450',
        ], ['Authorization' => "Bearer {$token}"]);
        $create->assertCreated();

        $articleId = $create->json('article.id');
        $update = $this->putJson("/api/articles/{$articleId}", [
            'title' => 'Judul Baru',
        ], ['Authorization' => "Bearer {$token}"]);
        $update->assertOk();

        $delete = $this->deleteJson("/api/articles/{$articleId}", [], ['Authorization' => "Bearer {$token}"]);
        $delete->assertStatus(403);
    }

    public function test_super_admin_can_delete_article_permanently(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'is_admin' => true,
        ]);
        $token = JWTAuth::fromUser($admin);

        $create = $this->postJson('/api/articles', [
            'title' => 'Judul',
            'content' => 'Konten',
            'category' => 'Teknologi',
            'image_url' => 'https://picsum.photos/seed/test-superadmin/800/450',
        ], ['Authorization' => "Bearer {$token}"]);
        $create->assertCreated();

        $articleId = $create->json('article.id');
        $delete = $this->deleteJson("/api/articles/{$articleId}", [], ['Authorization' => "Bearer {$token}"]);
        $delete->assertOk()->assertJson(['message' => 'Artikel berhasil dihapus secara permanen']);

        // Ensure deletion is permanent (cannot find article)
        $this->getJson("/api/articles/{$articleId}")->assertNotFound();

        // Deleting again should result in 404 (route model binding fails)
        $this->deleteJson("/api/articles/{$articleId}", [], ['Authorization' => "Bearer {$token}"])
            ->assertNotFound();

        // Log entry exists
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'delete',
            'model_id' => $articleId,
            'model_type' => Article::class,
        ]);
    }

    public function test_admin_can_edit_any_article(): void
    {
        $author = User::factory()->create();
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'is_admin' => true,
        ]);
        $authorToken = JWTAuth::fromUser($author);
        $adminToken = JWTAuth::fromUser($admin);

        // Author creates article
        $create = $this->postJson('/api/articles', [
            'title' => 'Judul',
            'content' => 'Konten',
            'category' => 'Teknologi',
            'image_url' => 'https://picsum.photos/seed/test-edit-admin/800/450',
        ], ['Authorization' => "Bearer {$authorToken}"]);
        $create->assertCreated();
        $articleId = $create->json('article.id');

        // Admin updates author's article
        $update = $this->putJson("/api/articles/{$articleId}", [
            'title' => 'Judul Diubah Oleh Admin',
        ], ['Authorization' => "Bearer {$adminToken}"]);
        $update->assertOk()->assertJsonPath('article.title', 'Judul Diubah Oleh Admin');
    }
}
