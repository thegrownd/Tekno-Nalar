<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\AccountDeleted;

class AdminController extends Controller
{
    public function pendingArticles(Request $request)
    {
        $query = Article::with('user:id,name')
            ->where('is_admin_post', false);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } elseif (!$request->has('status')) {
            // Default to pending if no status specified
            $query->where('status', 'pending');
        }

        $articles = $query->latest()->paginate(10);
            
        return response()->json($articles);
    }

    public function users()
    {
        $users = User::latest()->paginate(20);
        return response()->json($users);
    }
    
    public function toggleActive(User $user)
    {
        if ($user->isSuperAdmin()) {
            return response()->json(['message' => 'Cannot modify super admin'], 403);
        }
        
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'message' => "User {$status} successfully",
            'user' => $user
        ]);
    }

    public function backupUser(User $user)
    {
        $data = $user->load(['articles']);
        return response()->json($data);
    }
    
    public function deleteUser(User $user)
    {
        if ($user->isSuperAdmin()) {
             return response()->json(['message' => 'Cannot delete super admin'], 403);
        }

        // Email notification
        try {
            Mail::to($user->email)->send(new AccountDeleted($user));
        } catch (\Exception $e) {
            // Log error but continue deletion
            Log::error('Failed to send account deletion email: ' . $e->getMessage());
        }

        // Log the deletion
        ActivityLogService::logUserDeleted($user);

        $user->delete(); 
        
        return response()->json(['message' => 'User deleted successfully']);
    }
    
    public function auditLogs()
    {
        $logs = ActivityLog::with('user:id,name')->latest()->paginate(20);
        return response()->json($logs);
    }

    public function approveArticle(Request $request, Article $article)
    {
        // Validate that article is pending
        if ($article->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending articles can be approved'
            ], 400);
        }

        $validated = $request->validate([
            'feedback' => ['nullable', 'string', 'max:1000']
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $article->status;
            
            // Update article status
            $article->update([
                'status' => 'published',
                'feedback' => $validated['feedback'] ?? null
            ]);

            // Create notification for article owner
            $message = 'Artikel Anda "' . $article->title . '" telah disetujui dan diterbitkan.';
            if (!empty($validated['feedback'])) {
                $message .= ' Feedback: ' . $validated['feedback'];
            }

            Notification::create([
                'user_id' => $article->user_id,
                'type' => 'article_approved',
                'message' => $message,
            ]);

            // Log the approval
            ActivityLogService::logArticleApproved($article, $validated['feedback'] ?? null);

            DB::commit();

            return response()->json([
                'message' => 'Artikel berhasil disetujui dan diterbitkan',
                'article' => $article->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve article: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal menyetujui artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectArticle(Request $request, Article $article)
    {
        // Validate that article is pending
        if ($article->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending articles can be rejected'
            ], 400);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000']
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $article->status;
            
            // Update article status
            $article->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['reason'],
                'feedback' => $validated['reason'] // Also store in feedback for consistency
            ]);

            // Create notification for article owner
            $message = 'Artikel Anda "' . $article->title . '" ditolak. Alasan: ' . $validated['reason'];

            Notification::create([
                'user_id' => $article->user_id,
                'type' => 'article_rejected',
                'message' => $message,
            ]);

            // Log the rejection
            ActivityLogService::logArticleRejected($article, $validated['reason']);

            DB::commit();

            return response()->json([
                'message' => 'Artikel berhasil ditolak',
                'article' => $article->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject article: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal menolak artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVerificationStats()
    {
        $today = now()->startOfDay();
        
        $stats = [
            'pending_count' => Article::where('status', 'pending')->count(),
            'approved_today' => Article::where('status', 'published')
                ->whereDate('updated_at', $today)
                ->where('is_admin_post', false)
                ->count(),
            'rejected_today' => Article::where('status', 'rejected')
                ->whereDate('updated_at', $today)
                ->count(),
            'total_pending_users' => Article::where('status', 'pending')
                ->distinct('user_id')
                ->count('user_id'),
        ];

        return response()->json($stats);
    }
}
