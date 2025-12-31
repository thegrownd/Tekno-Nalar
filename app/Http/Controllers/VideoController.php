<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::with('user:id,name')->latest()->get();
        return response()->json($videos);
    }

    public function store(Request $request)
    {
        // 1. Autentikasi dan Otorisasi (Cek Admin)
        $user = auth('api')->user();
        if (!$user || !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized. Only admins can upload videos.'], 403);
        }

        // 2. Validasi Input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_file' => 'required|file|mimes:mp4,mov,ogg,qt|max:51200', // Max 50MB
            'thumbnail_file' => 'nullable|mimes:jpeg,png,jpg|max:10240', // Max 10MB
            'duration' => 'nullable|string',
        ]);

        try {
            // 3. Upload File Video
            if ($request->hasFile('video_file')) {
                $videoPath = $request->file('video_file')->store('videos', 'public');
            }

            // 4. Upload Thumbnail (jika ada)
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail_file')) {
                $thumbnailPath = $request->file('thumbnail_file')->store('thumbnails', 'public');
            } else {
                // Default thumbnail jika tidak ada
                $thumbnailPath = 'thumbnails/default-video.jpg'; 
            }

            // 5. Simpan ke Database
            $video = Video::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => '/storage/' . $videoPath,
                'thumbnail_path' => $thumbnailPath ? '/storage/' . $thumbnailPath : null,
                'duration' => $request->duration ?? '00:00',
            ]);

            // 6. Logging Aktivitas
            ActivityLogService::logVideoUploaded($video);
            
            // Log ke Laravel log juga sebagai backup
            Log::info("Video uploaded by admin {$user->id}: {$video->title}");

            return response()->json([
                'message' => 'Video uploaded successfully',
                'video' => $video
            ], 201);

        } catch (\Exception $e) {
            Log::error("Video upload failed: " . $e->getMessage());
            return response()->json(['message' => 'Video upload failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function incrementViews($id)
    {
        $video = Video::findOrFail($id);
        $video->increment('views');
        return response()->json(['message' => 'View count incremented', 'views' => $video->views]);
    }

    public function destroy($id)
    {
        // 1. Otorisasi (Sudah dihandle middleware, tapi double check aman)
        $user = auth('api')->user();
        if (!$user || !$user->is_admin) {
             ActivityLogService::logUnauthorizedAccess();
             return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $video = Video::findOrFail($id);
            
            // 2. Logging sebelum hapus
            ActivityLogService::logVideoDeleted($video);
            
            // 3. Soft Delete (Backup otomatis via SoftDeletes)
            $video->delete();
            
            return response()->json(['message' => 'Video deleted successfully (Soft Deleted)']);
            
        } catch (\Exception $e) {
            Log::error("Video deletion failed: " . $e->getMessage());
            return response()->json(['message' => 'Video deletion failed', 'error' => $e->getMessage()], 500);
        }
    }
}
