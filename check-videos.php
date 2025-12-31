<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING VIDEOS TABLE ===\n\n";

$videos = DB::table('videos')->get();

if ($videos->isEmpty()) {
    echo "❌ No videos found in database\n";
} else {
    echo "✅ Found " . $videos->count() . " video(s)\n\n";
    
    foreach ($videos as $video) {
        echo "Video ID: {$video->id}\n";
        echo "Title: {$video->title}\n";
        echo "File Path: {$video->file_path}\n";
        echo "Thumbnail Path: {$video->thumbnail_path}\n";
        
        // Check if thumbnail file exists
        if ($video->thumbnail_path) {
            $thumbnailPath = str_replace('/storage/', 'storage/app/public/', $video->thumbnail_path);
            $fullPath = __DIR__ . '/' . $thumbnailPath;
            
            if (file_exists($fullPath)) {
                echo "✅ Thumbnail file EXISTS: {$fullPath}\n";
                echo "   File size: " . filesize($fullPath) . " bytes\n";
            } else {
                echo "❌ Thumbnail file NOT FOUND: {$fullPath}\n";
            }
        } else {
            echo "⚠️  No thumbnail path in database\n";
        }
        
        echo "\n" . str_repeat("-", 80) . "\n\n";
    }
}

echo "\n=== CHECKING STORAGE STRUCTURE ===\n\n";

$thumbnailDir = __DIR__ . '/storage/app/public/thumbnails';
if (is_dir($thumbnailDir)) {
    echo "✅ Thumbnails directory exists: {$thumbnailDir}\n";
    
    $files = scandir($thumbnailDir);
    $files = array_diff($files, ['.', '..', '.gitignore']);
    
    if (empty($files)) {
        echo "⚠️  Directory is empty\n";
    } else {
        echo "✅ Found " . count($files) . " file(s) in thumbnails directory:\n";
        foreach ($files as $file) {
            $size = filesize($thumbnailDir . '/' . $file);
            echo "   - {$file} (" . number_format($size) . " bytes)\n";
        }
    }
} else {
    echo "❌ Thumbnails directory NOT FOUND: {$thumbnailDir}\n";
}

echo "\n=== CHECKING PUBLIC SYMLINK ===\n\n";

$publicStorage = __DIR__ . '/public/storage';
if (is_link($publicStorage)) {
    echo "✅ Symlink exists: {$publicStorage}\n";
    echo "   Points to: " . readlink($publicStorage) . "\n";
} elseif (is_dir($publicStorage)) {
    echo "⚠️  public/storage exists but is NOT a symlink (it's a directory)\n";
} else {
    echo "❌ public/storage does NOT exist\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
