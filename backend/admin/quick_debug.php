<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Quick Debug</title>
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
        <h1>🚨 Quick Debug</h1>
        <p>Frontend shows 'Failed to load music data' - let's find out why.</p>";

// Test 1: Direct API call
echo "<h2>📡 Test 1: Direct API Call</h2>";
$api_url = 'http://localhost/Portfolio/backend/api/music.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<p><strong>URL:</strong> <a href='$api_url' target='_blank'>$api_url</a></p>";
echo "<p><strong>HTTP Code:</strong> $http_code</p>";

if ($response !== false && $http_code === 200) {
    echo "<p class='success'>✅ API responded</p>";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ Valid JSON</p>";
        
        if ($data['success']) {
            echo "<p class='success'>✅ API reports success</p>";
            
            $album_count = isset($data['data']['albums_only']) ? count($data['data']['albums_only']) : 0;
            $single_count = isset($data['data']['singles']) ? count($data['data']['singles']) : 0;
            
            echo "<p><strong>Albums:</strong> $album_count</p>";
            echo "<p><strong>Singles:</strong> $single_count</p>";
            
            if ($album_count === 0 && $single_count === 0) {
                echo "<p class='error'>❌ No music data found in database</p>";
                echo "<p class='info'>Add some albums in Admin Hub first!</p>";
            } else {
                echo "<p class='success'>✅ Data available for frontend</p>";
            }
        } else {
            echo "<p class='error'>❌ API error: " . htmlspecialchars($data['error'] ?? 'Unknown') . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
        echo "<h4>Raw Response:</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<p class='error'>❌ API call failed</p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($curl_error) . "</p>";
}

// Test 2: Database check
echo "<h2>🗄️ Test 2: Database Check</h2>";
try {
    require_once '../config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        echo "<p class='success'>✅ Database connected</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM albums");
        $count = $stmt->fetchColumn();
        echo "<p><strong>Albums in database:</strong> $count</p>";
        
        if ($count > 0) {
            echo "<p class='success'>✅ Albums exist in database</p>";
        } else {
            echo "<p class='error'>❌ No albums in database</p>";
            echo "<p class='info'><a href='admin_hub.php'>Add albums in Admin Hub</a></p>";
        }
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>
            <h3>🎯 Solutions:</h3>
            <ol>
                <li><strong>If API fails:</strong> Check server configuration and file permissions</li>
                <li><strong>If no albums:</strong> Add albums in Admin Hub</li>
                <li><strong>If database fails:</strong> Check database connection</li>
                <li><strong>If API works:</strong> Issue is likely CORS or network in frontend</li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 Test API</a>
            <a href='quick_album_fix.php' class='btn' style='background: #ffc107; color: #000;'>🔧 Fix Database</a>
        </div>
    </div>
</body>
</html>";
?>
