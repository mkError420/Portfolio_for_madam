<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>API Debug Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 API Debug Tool</h1>
        <p>This will help diagnose why the frontend can't load music data.</p>";

// Test 1: Check if API file exists
echo "<div class='test-section'>";
echo "<h3>📁 Test 1: API File Check</h3>";
$api_file = '../api/music.php';
if (file_exists($api_file)) {
    echo "<p class='success'>✅ API file exists at: $api_file</p>";
} else {
    echo "<p class='error'>❌ API file NOT found at: $api_file</p>";
}
echo "</div>";

// Test 2: Check database connection
echo "<div class='test-section'>";
echo "<h3>🗄️ Test 2: Database Connection</h3>";
try {
    require_once '../config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        echo "<p class='success'>✅ Database connection successful</p>";
        
        // Test albums table
        $stmt = $pdo->query("SELECT COUNT(*) FROM albums");
        $count = $stmt->fetchColumn();
        echo "<p class='info'>📊 Albums in database: $count</p>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM albums LIMIT 3");
            $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>Sample albums:</h4>";
            echo "<pre>" . htmlspecialchars(json_encode($albums, JSON_PRETTY_PRINT)) . "</pre>";
        }
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// Test 3: Direct API call
echo "<div class='test-section'>";
echo "<h3>📡 Test 3: Direct API Call</h3>";
$api_url = 'http://localhost/Portfolio/backend/api/music.php';

// Test with file_get_contents
echo "<h4>Testing with file_get_contents:</h4>";
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'GET',
        'header' => "User-Agent: API-Debug-Tool\r\n"
    ]
]);

$response = @file_get_contents($api_url, false, $context);
if ($response !== false) {
    echo "<p class='success'>✅ API responded with file_get_contents</p>";
    echo "<h4>Response:</h4>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Parse JSON
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ Valid JSON response</p>";
    } else {
        echo "<p class='error'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
    }
} else {
    echo "<p class='error'>❌ file_get_contents failed</p>";
    $error = error_get_last();
    if ($error) {
        echo "<p class='info'>Error: " . htmlspecialchars($error['message']) . "</p>";
    }
}

// Test with cURL
echo "<h4>Testing with cURL:</h4>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response !== false && $http_code === 200) {
    echo "<p class='success'>✅ cURL successful (HTTP $http_code)</p>";
    echo "<h4>Response:</h4>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
} else {
    echo "<p class='error'>❌ cURL failed</p>";
    echo "<p class='info'>HTTP Code: $http_code</p>";
    echo "<p class='info'>cURL Error: " . htmlspecialchars($curl_error) . "</p>";
}
echo "</div>";

// Test 4: CORS Headers
echo "<div class='test-section'>";
echo "<h3>🌐 Test 4: CORS Headers Check</h3>";
echo "<p class='info'>The API should have these CORS headers:</p>";
echo "<pre>";
echo "Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE
Access-Control-Allow-Headers: Content-Type, Authorization";
echo "</pre>";

// Check if API file has CORS headers
if (file_exists($api_file)) {
    $api_content = file_get_contents($api_file);
    if (strpos($api_content, 'Access-Control-Allow-Origin') !== false) {
        echo "<p class='success'>✅ CORS headers found in API file</p>";
    } else {
        echo "<p class='warning'>⚠️ CORS headers might be missing</p>";
    }
}
echo "</div>";

// Test 5: Frontend URL check
echo "<div class='test-section'>";
echo "<h3>🔗 Test 5: Frontend URL Check</h3>";
$frontend_urls = [
    'http://localhost:3000' => 'React Development Server',
    'http://localhost:3001' => 'Alternative React Port',
    'http://127.0.0.1:3000' => 'Localhost Alternative',
    'http://localhost/Portfolio/frontend/public/index.html' => 'Static HTML'
];

foreach ($frontend_urls as $url => $description) {
    echo "<h4>Testing: $description ($url)</h4>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code > 0) {
        echo "<p class='success'>✅ $url is accessible (HTTP $http_code)</p>";
    } else {
        echo "<p class='error'>❌ $url is not accessible</p>";
    }
}
echo "</div>";

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>
            <h3>🎯 Solutions:</h3>
            <ol>
                <li><strong>If API file missing:</strong> The API file should exist at backend/api/music.php</li>
                <li><strong>If database fails:</strong> Check database connection and table structure</li>
                <li><strong>If CORS issue:</strong> Add CORS headers to API file</li>
                <li><strong>If frontend can't reach API:</strong> Check if React app is running on correct port</li>
                <li><strong>If all else fails:</strong> Try accessing API directly in browser</li>
            </ol>
            
            <p><strong>Direct API Test:</strong> <a href='../api/music.php' target='_blank' class='btn'>Open API in Browser</a></p>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='test_music_api.php' class='btn'>🎵 Music API Test</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 View API</a>
        </div>
    </div>
</body>
</html>";
?>
