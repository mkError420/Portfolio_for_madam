<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Check API Response</title>
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
        <h1>🔍 Check API Response Structure</h1>
        <p>This will verify the API is returning the correct data structure for the frontend.</p>";

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

if ($response !== false && $http_code === 200) {
    echo "<p class='success'>✅ API responded successfully</p>";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ Valid JSON</p>";
        
        echo "<h3>📊 Response Structure:</h3>";
        echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
        
        if ($data['success']) {
            echo "<h3>✅ Data Validation:</h3>";
            
            // Check albums_only
            if (isset($data['data']['albums_only'])) {
                if (is_array($data['data']['albums_only'])) {
                    echo "<p class='success'>✅ albums_only is an array with " . count($data['data']['albums_only']) . " items</p>";
                    
                    if (count($data['data']['albums_only']) > 0) {
                        $first_album = $data['data']['albums_only'][0];
                        echo "<h4>First Album Structure:</h4>";
                        echo "<pre>" . htmlspecialchars(json_encode($first_album, JSON_PRETTY_PRINT)) . "</pre>";
                        
                        // Check required fields
                        $required_fields = ['id', 'title', 'artist', 'type', 'cover'];
                        foreach ($required_fields as $field) {
                            if (isset($first_album[$field])) {
                                echo "<p class='success'>✅ $field: " . htmlspecialchars($first_album[$field]) . "</p>";
                            } else {
                                echo "<p class='error'>❌ Missing field: $field</p>";
                            }
                        }
                        
                        // Check tracks
                        if (isset($first_album['tracks']) && is_array($first_album['tracks'])) {
                            echo "<p class='success'>✅ tracks is an array with " . count($first_album['tracks']) . " items</p>";
                        } else {
                            echo "<p class='info'>ℹ️ No tracks array (this is ok)</p>";
                        }
                    }
                } else {
                    echo "<p class='error'>❌ albums_only is not an array</p>";
                }
            } else {
                echo "<p class='error'>❌ albums_only not found in response</p>";
            }
            
            // Check singles
            if (isset($data['data']['singles'])) {
                if (is_array($data['data']['singles'])) {
                    echo "<p class='success'>✅ singles is an array with " . count($data['data']['singles']) . " items</p>";
                } else {
                    echo "<p class='error'>❌ singles is not an array</p>";
                }
            } else {
                echo "<p class='error'>❌ singles not found in response</p>";
            }
            
        } else {
            echo "<p class='error'>❌ API reports error: " . htmlspecialchars($data['error'] ?? 'Unknown') . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
    }
} else {
    echo "<p class='error'>❌ API call failed (HTTP $http_code)</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>
            <h3>🎯 Frontend Expectations:</h3>
            <p>The frontend expects this structure:</p>
            <pre>
{
  \"success\": true,
  \"data\": {
    \"albums_only\": [
      {
        \"id\": 1,
        \"title\": \"Album Title\",
        \"artist\": \"Artist Name\",
        \"type\": \"album\",
        \"cover\": \"image_url\",
        \"tracks\": [...]
      }
    ],
    \"singles\": [...]
  }
}
            </pre>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 View API</a>
        </div>
    </div>
</body>
</html>";
?>
