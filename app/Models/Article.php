<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'category',
        'image_url',
        'user_id',
        'status',
        'rejection_reason',
        'feedback',
        'is_admin_post',
        'views_count',
        'weekly_views_count',
        'is_historically_popular',
    ];

    protected $casts = [
        'is_admin_post' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get activity logs for this article
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model', 'model_type', 'model_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if article can be deleted
     */
    public function canBeDeleted()
    {
        // Add any business logic here
        // For example: check if article has comments, likes, etc.
        return true;
    }
}
