<?php

echo "=== TESTING THUMBNAIL ACCESS ===\n\n";

$baseUrl = 'http://localhost';
$thumbnails = [
    'KZpirqg3vpkTzO4rRpdmtoaBL15J7voY919Lyup3.jpg',
    'yoakB2I4mgtN8iIkI10epKPU6ocNqMWaFVNdIobQ.png'
];

foreach ($thumbnails as $thumbnail) {
    $url = "{$baseUrl}/storage/thumbnails/{$thumbnail}";
    echo "Testing: {$url}\n";
    
    // Test with file_get_contents
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'ignore_errors' => true
        ]
    ]);
    
    $headers = @get_headers($url, 1, $context);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "✅ SUCCESS - File accessible (200 OK)\n";
        if (isset($headers['Content-Length'])) {
            echo "   Size: " . number_format($headers['Content-Length']) . " bytes\n";
        }
        if (isset($headers['Content-Type'])) {
            echo "   Type: {$headers['Content-Type']}\n";
        }
    } elseif ($headers && strpos($headers[0], '404') !== false) {
        echo "❌ FAILED - File not found (404)\n";
    } else {
        echo "⚠️  WARNING - Could not connect to server\n";
        echo "   Response: " . ($headers ? $headers[0] : 'No response') . "\n";
    }
    
    echo "\n";
}

echo "=== TESTING API ENDPOINTS ===\n\n";

// Test GET /api/videos
echo "Testing: GET {$baseUrl}/api/videos\n";
$ch = curl_init("{$baseUrl}/api/videos");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ SUCCESS - API endpoint working (200 OK)\n";
    
    // Parse response body
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    $data = json_decode($body, true);
    
    if ($data && is_array($data)) {
        echo "   Found " . count($data) . " video(s)\n";
    }
} else {
    echo "❌ FAILED - HTTP {$httpCode}\n";
}

echo "\n=== TEST COMPLETE ===\n";
