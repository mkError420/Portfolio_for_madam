<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>CORS Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🌐 CORS Fix Test</h1>
        <p>Testing if the CORS fix resolves the frontend error.</p>";

echo "<h2>📡 Test API with CORS Headers</h2>";
$api_url = 'http://localhost/Portfolio/backend/api/music.php';

// Test with CORS headers like React would send
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://127.0.0.1:3000',
    'Referer: http://127.0.0.1:3000/',
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<p><strong>Testing:</strong> $api_url</p>";
echo "<p><strong>HTTP Status:</strong> $http_code</p>";

if ($response !== false && $http_code === 200) {
    echo "<p class='success'>✅ API responded with CORS headers</p>";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ Valid JSON response</p>";
        
        if ($data['success']) {
            $album_count = isset($data['data']['albums_only']) ? count($data['data']['albums_only']) : 0;
            $single_count = isset($data['data']['singles']) ? count($data['data']['singles']) : 0;
            
            echo "<p class='success'>✅ Frontend should now work!</p>";
            echo "<p><strong>Albums:</strong> $album_count</p>";
            echo "<p><strong>Singles:</strong> $single_count</p>";
            
            if ($album_count === 0 && $single_count === 0) {
                echo "<p class='warning'>⚠️ No albums yet - add some in Admin Hub</p>";
            }
        } else {
            echo "<p class='error'>❌ API error: " . htmlspecialchars($data['error'] ?? 'Unknown') . "</p>";
        }
    } else {
        echo "<p class='error'>❌ JSON error: " . json_last_error_msg() . "</p>";
    }
} else {
    echo "<p class='error'>❌ API call failed</p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($curl_error) . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #d4edda; border-radius: 5px; border-left: 4px solid #28a745;'>
            <h3>✅ CORS Fix Applied:</h3>
            <ul>
                <li>Added <strong>Access-Control-Allow-Origin: *</strong></li>
                <li>Added <strong>Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS</strong></li>
                <li>Added <strong>Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With</strong></li>
                <li>Added <strong>OPTIONS request handler</strong> for preflight</li>
                <li>Added <strong>Access-Control-Max-Age: 86400</strong> for caching</li>
            </ul>
        </div>
        
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>
            <h3>🎯 Next Steps:</h3>
            <ol>
                <li><strong>Refresh React app:</strong> Go to http://127.0.0.1:3000/music</li>
                <li><strong>Check Network tab:</strong> Should see 200 OK instead of CORS error</li>
                <li><strong>If still fails:</strong> Clear browser cache and reload</li>
                <li><strong>If no albums:</strong> Add albums in Admin Hub</li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 Test API</a>
            <a href='http://127.0.0.1:3000/music' target='_blank' class='btn' style='background: #ffc107; color: #000;'>🎵 Test Frontend</a>
        </div>
    </div>
</body>
</html>";
?>
