<?php

echo "=== TESTING WITH ARTISAN SERVE (Port 8000) ===\n\n";

$baseUrl = 'http://localhost:8000';
$thumbnails = [
    'KZpirqg3vpkTzO4rRpdmtoaBL15J7voY919Lyup3.jpg',
    'yoakB2I4mgtN8iIkI10epKPU6ocNqMWaFVNdIobQ.png'
];

// Wait a bit for server to start
sleep(2);

foreach ($thumbnails as $thumbnail) {
    $url = "{$baseUrl}/storage/thumbnails/{$thumbnail}";
    echo "Testing: {$url}\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'ignore_errors' => true,
            'timeout' => 5
        ]
    ]);
    
    $headers = @get_headers($url, 1, $context);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "✅ SUCCESS - File accessible via Laravel route (200 OK)\n";
        if (isset($headers['Content-Length'])) {
            echo "   Size: " . number_format($headers['Content-Length']) . " bytes\n";
        }
        if (isset($headers['Content-Type'])) {
            echo "   Type: {$headers['Content-Type']}\n";
        }
    } elseif ($headers && strpos($headers[0], '404') !== false) {
        echo "❌ FAILED - File not found (404)\n";
    } else {
        echo "⚠️  WARNING - Could not connect\n";
        echo "   Response: " . ($headers ? $headers[0] : 'No response') . "\n";
    }
    
    echo "\n";
}

echo "=== TEST COMPLETE ===\n";
