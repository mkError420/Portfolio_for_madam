<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Music API</title>
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
        <h1>🎵 Music API Test</h1>
        <p>This will test if the music API is working and show your albums data.</p>";

try {
    // Test the API endpoint
    echo "<h2>📡 Testing API Endpoint</h2>";
    $api_url = 'http://localhost/Portfolio/backend/api/music.php';
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET'
        ]
    ]);
    
    $response = file_get_contents($api_url, false, $context);
    
    if ($response === false) {
        echo "<p class='error'>❌ Failed to connect to API</p>";
        echo "<p class='info'>Make sure your web server is running and the API file exists at: $api_url</p>";
    } else {
        echo "<p class='success'>✅ API responded successfully!</p>";
        
        // Parse JSON response
        $data = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "<h3>📊 API Response:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
            
            if ($data['success']) {
                echo "<h3>🎵 Albums Found:</h3>";
                if (empty($data['data']['albums_only'])) {
                    echo "<p class='info'>No albums found. Add some albums from the Admin Hub!</p>";
                    echo "<a href='admin_hub.php' class='btn'>🎛️ Go to Admin Hub</a>";
                } else {
                    echo "<table border='1' style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
                    echo "<tr><th>ID</th><th>Title</th><th>Artist</th><th>Type</th><th>Release Date</th><th>Genre</th></tr>";
                    
                    foreach ($data['data']['albums_only'] as $album) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($album['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($album['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($album['artist']) . "</td>";
                        echo "<td>" . htmlspecialchars($album['type']) . "</td>";
                        echo "<td>" . htmlspecialchars($album['release_date'] ?: 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($album['genre'] ?: 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    echo "<p class='success'>✅ " . count($data['data']['albums_only']) . " albums found!</p>";
                }
                
                echo "<h3>🎤 Singles Found:</h3>";
                if (empty($data['data']['singles'])) {
                    echo "<p class='info'>No singles found.</p>";
                } else {
                    echo "<table border='1' style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
                    echo "<tr><th>ID</th><th>Title</th><th>Artist</th><th>Release Date</th><th>Genre</th></tr>";
                    
                    foreach ($data['data']['singles'] as $single) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($single['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($single['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($single['artist']) . "</td>";
                        echo "<td>" . htmlspecialchars($single['release_date'] ?: 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($single['genre'] ?: 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    echo "<p class='success'>✅ " . count($data['data']['singles']) . " singles found!</p>";
                }
                
            } else {
                echo "<p class='error'>❌ API returned error: " . htmlspecialchars($data['error']) . "</p>";
            }
        } else {
            echo "<p class='error'>❌ Invalid JSON response: " . json_last_error_msg() . "</p>";
            echo "<h3>Raw Response:</h3>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>
            <h3>🎯 Next Steps:</h3>
            <ol>
                <li><strong>If API works:</strong> Your frontend should now show albums!</li>
                <li><strong>If no albums found:</strong> <a href='admin_hub.php' class='btn'>Add albums</a></li>
                <li><strong>If API fails:</strong> Check server configuration</li>
                <li><strong>Test frontend:</strong> <a href='../../frontend/public/index.html' class='btn' target='_blank'>View Website</a></li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../../frontend/public/index.html' class='btn' style='background: #28a745;' target='_blank'>🌐 View Website</a>
        </div>
    </div>
</body>
</html>";
?>
