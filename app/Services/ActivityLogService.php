<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\User;
use App\Models\Video;

class ActivityLogService
{
    public static function logArticleCreated(Article $article): void
    {
        self::log('create', $article, null, $article->only(['title', 'content', 'category', 'image_url']));
    }

    public static function logArticleUpdated(Article $article, array $oldValues): void
    {
        self::log('update', $article, $oldValues, $article->only(['title', 'content', 'category', 'image_url']));
    }

    public static function logArticleDeleted(Article $article): void
    {
        self::log('delete', $article, $article->only(['title', 'content', 'category', 'image_url']), null);
    }

    public static function logArticleRestored(Article $article): void
    {
        self::log('restore', $article, null, $article->only(['title', 'content', 'category', 'image_url']));
    }

    public static function getLogsFor(Article $article)
    {
        return ActivityLog::where('model_type', Article::class)
            ->where('model_id', $article->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public static function logGeneric(string $action, string $modelType, int $modelId, ?array $oldValues, ?array $newValues, ?string $description = null): void
    {
        ActivityLog::create([
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'user_id' => auth('api')->id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'description' => $description ?? "{$modelType} {$action}",
        ]);
    }

    protected static function log(string $action, Article $article, ?array $oldValues, ?array $newValues): void
    {
        self::logGeneric($action, Article::class, $article->id, $oldValues, $newValues, "Article {$action}");
    }

    public static function logAdminLogin(User $user, string $description): void
    {
        ActivityLog::create([
            'action' => 'admin_login',
            'model_type' => User::class,
            'model_id' => $user->id,
            'user_id' => $user->id,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'description' => $description,
        ]);
    }

    public static function logUnauthorizedAccess(): void
    {
        $user = auth('api')->user();
        $userId = $user ? $user->id : null;
        $description = $user 
            ? "Unauthorized access attempt by user {$user->name} ({$user->email}) to " . request()->path()
            : "Unauthorized access attempt by guest to " . request()->path();

        ActivityLog::create([
            'action' => 'unauthorized_access',
            'model_type' => User::class,
            'model_id' => $userId ?? 0, // 0 for guest
            'user_id' => $userId,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'description' => $description,
        ]);
    }

    public static function logUserStatusChanged(User $targetUser, string $status): void
    {
        self::logGeneric(
            'status_change', 
            User::class, 
            $targetUser->id, 
            ['is_active' => !$targetUser->is_active], 
            ['is_active' => $targetUser->is_active],
            "User {$targetUser->name} was {$status} by admin"
        );
    }

    public static function logUserDeleted(User $targetUser): void
    {
        self::logGeneric(
            'delete', 
            User::class, 
            $targetUser->id, 
            $targetUser->toArray(), 
            null,
            "User {$targetUser->name} was deleted by admin"
        );
    }

    public static function logVideoUploaded(Video $video): void
    {
        self::logGeneric(
            'upload',
            Video::class,
            $video->id,
            null,
            $video->toArray(),
            "Video '{$video->title}' was uploaded by admin"
        );
    }

    public static function logVideoDeleted(Video $video): void
    {
        self::logGeneric(
            'delete',
            Video::class,
            $video->id,
            $video->toArray(),
            null,
            "Video '{$video->title}' was deleted by admin"
        );
    }

    public static function logArticleApproved(Article $article, ?string $feedback = null): void
    {
        $description = "Article '{$article->title}' was approved and published";
        if ($feedback) {
            $description .= " with feedback: {$feedback}";
        }
        
        self::logGeneric(
            'approve',
            Article::class,
            $article->id,
            ['status' => 'pending'],
            ['status' => 'published', 'feedback' => $feedback],
            $description
        );
    }

    public static function logArticleRejected(Article $article, string $reason): void
    {
        $description = "Article '{$article->title}' was rejected. Reason: {$reason}";
        
        self::logGeneric(
            'reject',
            Article::class,
            $article->id,
            ['status' => 'pending'],
            ['status' => 'rejected', 'rejection_reason' => $reason],
            $description
        );
    }

    public static function logArticleStatusChanged(Article $article, string $oldStatus, string $newStatus, ?string $reason = null): void
    {
        $description = "Article '{$article->title}' status changed from {$oldStatus} to {$newStatus}";
        if ($reason) {
            $description .= ". Reason: {$reason}";
        }
        
        self::logGeneric(
            'status_change',
            Article::class,
            $article->id,
            ['status' => $oldStatus],
            ['status' => $newStatus],
            $description
        );
    }
}
