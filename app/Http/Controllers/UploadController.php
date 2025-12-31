<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'], // Max 5MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads', $filename);
            
            $url = Storage::url($path);
            
            // If local dev, ensure URL is correct
            if (app()->environment('local')) {
                $url = url($url);
            }

            return response()->json([
                'url' => $url,
                'message' => 'Upload berhasil'
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}