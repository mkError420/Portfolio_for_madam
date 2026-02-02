<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Data Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Data Check</h1>
        <p>Let's check what's in the database and why frontend isn't showing data.</p>";

// Check database connection and albums
echo "<h2>🗄️ Database Check</h2>";
try {
    require_once '../config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        echo "<p class='success'>✅ Database connected</p>";
        
        // Check albums table
        $stmt = $pdo->query("SELECT COUNT(*) FROM albums");
        $album_count = $stmt->fetchColumn();
        echo "<p><strong>Total albums in database:</strong> $album_count</p>";
        
        if ($album_count > 0) {
            echo "<p class='success'>✅ Albums exist in database</p>";
            
            // Show all albums
            $stmt = $pdo->query("SELECT * FROM albums ORDER BY created_at DESC");
            $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>📀 All Albums in Database:</h3>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Title</th><th>Artist</th><th>Type</th><th>Release Date</th><th>Genre</th><th>Created</th></tr>";
            
            foreach ($albums as $album) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($album['id']) . "</td>";
                echo "<td>" . htmlspecialchars($album['title']) . "</td>";
                echo "<td>" . htmlspecialchars($album['artist']) . "</td>";
                echo "<td>" . htmlspecialchars($album['type']) . "</td>";
                echo "<td>" . htmlspecialchars($album['release_date'] ?: 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($album['genre'] ?: 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($album['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            echo "<p class='error'>❌ No albums found in database</p>";
            echo "<p class='info'>You need to add albums first!</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test API response
echo "<h2>📡 API Response Test</h2>";
$api_url = 'http://localhost/Portfolio/backend/api/music.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>API URL:</strong> <a href='$api_url' target='_blank'>$api_url</a></p>";
echo "<p><strong>HTTP Status:</strong> $http_code</p>";

if ($response !== false && $http_code === 200) {
    echo "<p class='success'>✅ API responded</p>";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ Valid JSON</p>";
        
        if ($data['success']) {
            echo "<p class='success'>✅ API reports success</p>";
            
            $albums_only = $data['data']['albums_only'] ?? [];
            $singles = $data['data']['singles'] ?? [];
            
            echo "<p><strong>Albums for frontend:</strong> " . count($albums_only) . "</p>";
            echo "<p><strong>Singles for frontend:</strong> " . count($singles) . "</p>";
            
            if (count($albums_only) > 0) {
                echo "<h3>📊 Albums Data Sent to Frontend:</h3>";
                echo "<pre>" . htmlspecialchars(json_encode($albums_only, JSON_PRETTY_PRINT)) . "</pre>";
            } else {
                echo "<p class='warning'>⚠️ No albums data for frontend</p>";
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
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;'>
            <h3>🎯 Troubleshooting Steps:</h3>
            <ol>
                <li><strong>If no albums in database:</strong> Add albums in Admin Hub</li>
                <li><strong>If API fails:</strong> Check server and file permissions</li>
                <li><strong>If API works but frontend empty:</strong> Check browser console for errors</li>
                <li><strong>If data exists but not showing:</strong> Check React component state</li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 Test API</a>
            <a href='http://127.0.0.1:3000/music' target='_blank' class='btn' style='background: #ffc107; color: #000;'>🎵 Frontend</a>
        </div>
    </div>
</body>
</html>";
?>
