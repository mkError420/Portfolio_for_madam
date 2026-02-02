<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Simple API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Simple API Test</h1>
        <p>Quick test to see if the music API is working.</p>";

// Test API directly
echo "<h2>📡 Testing Music API</h2>";
$api_url = 'http://localhost/Portfolio/backend/api/music.php';

echo "<p><strong>URL:</strong> <a href='$api_url' target='_blank'>$api_url</a></p>";

// Use cURL to test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<h3>Results:</h3>";

if ($response !== false && $http_code === 200) {
    echo "<p class='success'>✅ API responded successfully (HTTP $http_code)</p>";
    
    // Parse and display JSON
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ Valid JSON response</p>";
        
        if ($data['success']) {
            echo "<p class='success'>✅ API reports success</p>";
            
            $album_count = isset($data['data']['albums_only']) ? count($data['data']['albums_only']) : 0;
            $single_count = isset($data['data']['singles']) ? count($data['data']['singles']) : 0;
            
            echo "<p><strong>Albums found:</strong> $album_count</p>";
            echo "<p><strong>Singles found:</strong> $single_count</p>";
            
            if ($album_count > 0) {
                echo "<h4>Sample Album:</h4>";
                $sample_album = $data['data']['albums_only'][0];
                echo "<pre>" . htmlspecialchars(json_encode($sample_album, JSON_PRETTY_PRINT)) . "</pre>";
            }
        } else {
            echo "<p class='error'>❌ API reports error: " . htmlspecialchars($data['error'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
        echo "<h4>Raw Response:</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<p class='error'>❌ API call failed</p>";
    echo "<p><strong>HTTP Code:</strong> $http_code</p>";
    echo "<p><strong>cURL Error:</strong> " . htmlspecialchars($curl_error) . "</p>";
    
    if ($response) {
        echo "<h4>Partial Response:</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>
            <h3>🎯 Next Steps:</h3>
            <ol>
                <li><strong>If API works:</strong> The issue is likely in the frontend (CORS, network, etc.)</li>
                <li><strong>If API fails:</strong> Check server configuration and database</li>
                <li><strong>Test in browser:</strong> Open the API URL directly in your browser</li>
                <li><strong>Check React console:</strong> Look for network errors in browser dev tools</li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 Open API Directly</a>
            <a href='debug_api.php' class='btn' style='background: #ffc107; color: #000;'>🔍 Full Debug</a>
        </div>
    </div>
</body>
</html>";
?>
