<?php

// Test API Endpoints
echo "=== Testing TeknoNalar API ===\n\n";

// Test 1: Login Admin
echo "1. Testing Admin Login...\n";
$ch = curl_init('http://localhost/api/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@gmail.com',
    'password' => 'Nagasaya1'
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Login Success!\n";
    echo "Token: " . substr($data['access_token'], 0, 20) . "...\n";
    echo "User: " . $data['user']['name'] . " (" . $data['user']['email'] . ")\n";
    echo "Is Admin: " . ($data['user']['is_admin'] ? 'Yes' : 'No') . "\n";
    $token = $data['access_token'];
} else {
    echo "❌ Login Failed!\n";
    echo "Response: $response\n";
    exit(1);
}

echo "\n";

// Test 2: Get Articles
echo "2. Testing Get Articles...\n";
$ch = curl_init('http://localhost/api/articles');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    $count = is_array($data['data']) ? count($data['data']) : count($data);
    echo "✅ Get Articles Success!\n";
    echo "Total Articles: $count\n";
} else {
    echo "❌ Get Articles Failed!\n";
}

echo "\n";

// Test 3: Create Article (with auth)
echo "3. Testing Create Article (Admin)...\n";
$ch = curl_init('http://localhost/api/articles');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'title' => 'Test Article - ' . date('Y-m-d H:i:s'),
    'content' => 'This is a test article created by automated testing script. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'category' => 'Teknologi',
    'image_url' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600'
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";
if ($httpCode === 201) {
    $data = json_decode($response, true);
    echo "✅ Create Article Success!\n";
    echo "Article ID: " . $data['id'] . "\n";
    echo "Title: " . $data['title'] . "\n";
    $testArticleId = $data['id'];
} else {
    echo "❌ Create Article Failed!\n";
    echo "Response: $response\n";
}

echo "\n";

// Test 4: Get Single Article
if (isset($testArticleId)) {
    echo "4. Testing Get Single Article...\n";
    $ch = curl_init("http://localhost/api/articles/$testArticleId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Status Code: $httpCode\n";
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✅ Get Single Article Success!\n";
        echo "Title: " . $data['title'] . "\n";
        echo "Category: " . $data['category'] . "\n";
    } else {
        echo "❌ Get Single Article Failed!\n";
    }

    echo "\n";

    // Test 5: Delete Article
    echo "5. Testing Delete Article (Admin)...\n";
    $ch = curl_init("http://localhost/api/articles/$testArticleId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Status Code: $httpCode\n";
    if ($httpCode === 200 || $httpCode === 204) {
        echo "✅ Delete Article Success!\n";
    } else {
        echo "❌ Delete Article Failed!\n";
        echo "Response: $response\n";
    }
}

echo "\n=== Testing Complete ===\n";
