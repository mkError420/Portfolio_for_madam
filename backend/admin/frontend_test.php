<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Frontend Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🌐 Frontend Connection Test</h1>
        <p>Test the exact same API call that React is making.</p>";

echo "<h2>🧪 JavaScript Fetch Test (Same as React)</h2>";
echo "<p>This is the exact code your React Music.js is running:</p>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>
const response = await fetch('http://localhost/Portfolio/backend/api/music.php');
const data = await response.json();

if (data.success) {
    setAlbums(data.data.albums_only || []);
    setSingles(data.data.singles || []);
} else {
    setError('Failed to load music data');
}
</pre>";

echo "<h2>📡 Test the API Call</h2>";
$api_url = 'http://localhost/Portfolio/backend/api/music.php';

// Test with headers that React would send
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<p><strong>React will call:</strong> $api_url</p>";
echo "<p><strong>HTTP Status:</strong> $http_code</p>";

if ($response !== false && $http_code === 200) {
    echo "<p class='success'>✅ API call successful</p>";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ JSON parsed successfully</p>";
        
        if ($data['success']) {
            echo "<p class='success'>✅ Frontend should receive this data</p>";
            
            $albums = $data['data']['albums_only'] ?? [];
            $singles = $data['data']['singles'] ?? [];
            
            echo "<p><strong>Albums for frontend:</strong> " . count($albums) . "</p>";
            echo "<p><strong>Singles for frontend:</strong> " . count($singles) . "</p>";
            
            if (count($albums) === 0 && count($singles) === 0) {
                echo "<p class='error'>❌ No data for frontend - add albums in Admin Hub!</p>";
            }
        } else {
            echo "<p class='error'>❌ Frontend will show error: " . htmlspecialchars($data['error'] ?? 'Unknown') . "</p>";
        }
    } else {
        echo "<p class='error'>❌ JSON parse error - frontend will fail</p>";
        echo "<p class='info'>Error: " . json_last_error_msg() . "</p>";
    }
} else {
    echo "<p class='error'>❌ API call failed - frontend will show 'Failed to load music data'</p>";
    echo "<p class='info'>HTTP Code: $http_code</p>";
    echo "<p class='info'>Error: " . htmlspecialchars($curl_error) . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;'>
            <h3>🔥 Most Likely Issues:</h3>
            <ol>
                <li><strong>No albums in database:</strong> Add albums in Admin Hub</li>
                <li><strong>CORS issue:</strong> API has CORS headers, but React might still block</li>
                <li><strong>Network issue:</strong> React can't reach localhost from 127.0.0.1:3000</li>
                <li><strong>Server not running:</strong> Apache/XAMPP not started</li>
            </ol>
        </div>
        
        <div style='margin-top: 30px; padding: 20px; background: #d4edda; border-radius: 5px; border-left: 4px solid #28a745;'>
            <h3>✅ Quick Fixes:</h3>
            <ol>
                <li><strong>Add albums:</strong> <a href='admin_hub.php' class='btn'>Admin Hub</a></li>
                <li><strong>Check API:</strong> <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>Test API</a></li>
                <li><strong>Check React console:</strong> Press F12 in browser, look at Network tab</li>
                <li><strong>Try different URL:</strong> Change fetch to use 127.0.0.1 instead of localhost</li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='quick_album_fix.php' class='btn' style='background: #ffc107; color: #000;'>🔧 Fix Database</a>
            <a href='http://127.0.0.1:3000' target='_blank' class='btn' style='background: #6c757d;'>🚀 React App</a>
        </div>
    </div>
</body>
</html>";
?>
