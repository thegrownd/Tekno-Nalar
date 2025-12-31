<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageController extends Controller
{
    /**
     * Serve files from storage/app/public
     */
    public function serve(Request $request, $path)
    {
        // Validate path to prevent directory traversal
        $path = str_replace(['../', '..\\'], '', $path);
        
        // Check if file exists in public disk
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }
        
        // Get file path
        $filePath = Storage::disk('public')->path($path);
        
        // Get MIME type
        $mimeType = mime_content_type($filePath);
        
        // Return file response
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
