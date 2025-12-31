<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use App\Models\Notification;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('user:id,name')
            ->where('status', 'published')
            ->latest()
            ->paginate(10);

        return response()->json($articles);
    }

    public function myArticles()
    {
        $articles = Article::where('user_id', auth('api')->id())
            ->latest()
            ->paginate(10);
        
        return response()->json($articles);
    }

    public function show(Article $article)
    {
        // Check visibility: Owner OR Admin OR Published
        $user = auth('api')->user();
        $isAdmin = $user && (bool) ($user->is_admin ?? false);
        $isOwner = $user && $article->user_id === $user->id;

        if ($article->status !== 'published' && !$isOwner && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Increment view counters
        $article->increment('views_count');
        $article->increment('weekly_views_count');

        $article->load('user:id,name');

        return response()->json($article);
    }

    public function popular()
    {
        // Get top 10 articles by weekly views
        // Exclude articles that have been popular in previous weeks
        // Cache for 10 minutes to handle high traffic
        $articles = \Illuminate\Support\Facades\Cache::remember('articles.popular', 600, function () {
            return Article::with('user:id,name')
                ->where('status', 'published')
                ->where('is_historically_popular', false) // Only show new popular articles
                ->orderByDesc('weekly_views_count')
                ->take(10)
                ->get();
        });

        return response()->json($articles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:50'],
            'category' => ['required', 'string', Rule::in(['Cyber Security', 'Pemrograman', 'Teknologi', 'Sistem Digital'])],
            'image_url' => ['nullable', 'url', 'required_without:image_file'],
            'image_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:5120', 'required_without:image_url'],
        ]);

        DB::beginTransaction();
        try {
            // Handle Image Upload if present
            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('article_images', 'public');
                $validated['image_url'] = '/storage/' . $path;
                unset($validated['image_file']);
            }

            $user = auth('api')->user();
            $isAdmin = $user && (bool) ($user->is_admin ?? false);
            
            // Default status: Published for Admin, Pending for regular users
            $status = $isAdmin ? 'published' : 'pending';
            
            // Allow Admin to override status if provided
            if ($isAdmin && $request->has('status') && in_array($request->status, ['published', 'draft', 'pending'])) {
                $status = $request->status;
            }

            $article = Article::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'image_url' => $validated['image_url'],
                'user_id' => auth('api')->id(),
                'status' => $status,
                'is_admin_post' => $isAdmin, // Set flag automatically
            ]);

            // Notify admins
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                $msg = $isAdmin 
                    ? 'Artikel baru "' . $article->title . '" telah diterbitkan oleh Admin.'
                    : 'Artikel baru "' . $article->title . '" menunggu verifikasi.';

                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'new_article_submission',
                    'message' => $msg,
                ]);
            }

            // Log the creation
            ActivityLogService::logArticleCreated($article);

            DB::commit();

            return response()->json([
                'message' => 'Artikel berhasil dibuat dan menunggu verifikasi admin',
                'article' => $article
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create article: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal membuat artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Article $article)
    {
        $user = auth('api')->user();
        $isAdmin = $user && (bool) ($user->is_admin ?? false);
        $isOwner = $article->user_id === auth('api')->id();
        
        if (! $isOwner && ! $isAdmin) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengubah artikel ini'
            ], 403);
        }

        $rules = [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'category' => ['sometimes', 'required', 'string', Rule::in(['Cyber Security', 'Pemrograman', 'Teknologi', 'Sistem Digital'])],
            'image_url' => ['nullable', 'url'],
            'image_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];

        if ($isAdmin) {
            $rules['status'] = ['sometimes', 'required', Rule::in(['draft', 'pending', 'published', 'rejected'])];
            $rules['rejection_reason'] = ['sometimes', 'nullable', 'string'];
            $rules['feedback'] = ['sometimes', 'nullable', 'string'];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Store old values for logging
            $oldValues = $article->only(['title', 'content', 'category', 'image_url', 'status']);
            $oldStatus = $article->status;

            // Handle Image Upload if present
            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('article_images', 'public');
                $validated['image_url'] = '/storage/' . $path;
                unset($validated['image_file']);
            }

            // If user updates (not admin), reset status to pending
            if (!$isAdmin) {
                $validated['status'] = 'pending';
            }

            $article->update($validated);

            // Notify owner if status changed (by Admin)
            if ($isAdmin && isset($validated['status']) && $validated['status'] !== $oldStatus && $article->user_id !== $user->id) {
                 $message = '';
                 $feedback = $validated['feedback'] ?? ($validated['rejection_reason'] ?? '');
                 
                 if ($validated['status'] === 'published') {
                     $message = 'Artikel Anda "' . $article->title . '" telah diterbitkan.';
                     if ($feedback) $message .= " Feedback: $feedback";
                 } elseif ($validated['status'] === 'rejected') {
                     $reason = $feedback ?: 'Tidak ada alasan.';
                     $message = 'Artikel Anda "' . $article->title . '" ditolak. Alasan: ' . $reason;
                 }
                 
                 if ($message) {
                     Notification::create([
                         'user_id' => $article->user_id,
                         'type' => 'article_status_update',
                         'message' => $message,
                     ]);
                 }
            }

            // Log the update
            ActivityLogService::logArticleUpdated($article, $oldValues);

            DB::commit();

            return response()->json([
                'message' => 'Artikel berhasil diperbarui',
                'article' => $article
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update article: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal memperbarui artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Article $article)
    {
        // Check if article can be deleted (business logic)
        // Admin can delete any article, Owner can delete their own
        $user = auth('api')->user();
        $isAdmin = $user && (bool) ($user->is_admin ?? false);
        $isOwner = $article->user_id === $user->id;

        if (!$isOwner && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            // Log the deletion BEFORE deleting (so we still have the data)
            ActivityLogService::logArticleDeleted($article);

            // Permanently delete the article
            $article->forceDelete();

            DB::commit();

            return response()->json([
                'message' => 'Artikel berhasil dihapus secara permanen',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete article: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal menghapus artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logs(Article $article)
    {
        $user = auth('api')->user();
        $isAdmin = $user && (bool) ($user->is_admin ?? false);
        $isOwner = $article->user_id === auth('api')->id();

        if (!$isOwner && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logs = $article->activityLogs()->with('user:id,name')->latest()->get();

        return response()->json($logs);
    }
}
