<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index($articleId)
    {
        $comments = Comment::where('article_id', $articleId)
            ->whereNull('parent_id')
            ->with(['user:id,name,profile_picture_url', 'replies.user:id,name,profile_picture_url'])
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }

    public function store(Request $request, $articleId)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $article = Article::findOrFail($articleId);
        $user = Auth::user();

        // Spam protection: Check if user commented in last 10 seconds
        $lastComment = Comment::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($lastComment && $lastComment->created_at->diffInSeconds(now()) < 10) {
            return response()->json(['message' => 'Tunggu sebentar sebelum mengirim komentar lagi.'], 429);
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content
        ]);

        // Create Notification
        // 1. Notify Article Owner if someone comments (unless owner comments on own article)
        if (!$request->parent_id && $article->user_id !== $user->id) {
            Notification::create([
                'user_id' => $article->user_id,
                'type' => 'comment',
                'title' => 'Komentar Baru',
                'message' => "{$user->name} mengomentari artikel Anda: {$article->title}",
                'data' => json_encode(['article_id' => $article->id, 'comment_id' => $comment->id])
            ]);
        }

        // 2. Notify Comment Owner if someone replies
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if ($parentComment->user_id !== $user->id) {
                Notification::create([
                    'user_id' => $parentComment->user_id,
                    'type' => 'reply',
                    'title' => 'Balasan Baru',
                    'message' => "{$user->name} membalas komentar Anda di artikel: {$article->title}",
                    'data' => json_encode(['article_id' => $article->id, 'comment_id' => $comment->id])
                ]);
            }
        }

        $comment->load('user:id,name,profile_picture_url');

        return response()->json($comment, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|min:3|max:1000'
        ]);

        $comment->update(['content' => $request->content]);

        return response()->json($comment);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        // Allow delete if user is owner of comment OR admin
        if ($comment->user_id !== $user->id && !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentar dihapus']);
    }
}
