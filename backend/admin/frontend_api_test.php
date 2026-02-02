<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Frontend API Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🌐 Frontend API Connection Test</h1>
        <p>Testing the connection between React frontend (http://127.0.0.1:3000) and backend API.</p>";

// Test 1: Check if React app is running
echo "<div class='test-section'>";
echo "<h3>🚀 Test 1: React App Status</h3>";
$react_url = 'http://127.0.0.1:3000';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $react_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_NOBODY, true);

curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code > 0) {
    echo "<p class='success'>✅ React app is running at $react_url (HTTP $http_code)</p>";
} else {
    echo "<p class='error'>❌ React app is NOT running at $react_url</p>";
    echo "<p class='info'>Solution: Start the React app with 'npm start' in the frontend directory</p>";
}
echo "</div>";

// Test 2: API from React's perspective
echo "<div class='test-section'>";
echo "<h3>📡 Test 2: API from React's Perspective</h3>";
$api_url = 'http://localhost/Portfolio/backend/api/music.php';

echo "<p><strong>React will try to fetch:</strong> $api_url</p>";

// Test the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true); // Include headers

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<h4>HTTP Response:</h4>";
echo "<p><strong>Status Code:</strong> $http_code</p>";

if ($http_code === 200) {
    echo "<p class='success'>✅ API responded successfully</p>";
    
    // Check CORS headers
    if (strpos($headers, 'Access-Control-Allow-Origin') !== false) {
        echo "<p class='success'>✅ CORS headers found</p>";
    } else {
        echo "<p class='warning'>⚠️ CORS headers might be missing</p>";
        echo "<p class='code'>Headers received:</p>";
        echo "<pre>" . htmlspecialchars($headers) . "</pre>";
    }
    
    // Parse JSON
    $data = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<p class='success'>✅ Valid JSON response</p>";
        
        if ($data['success']) {
            $album_count = isset($data['data']['albums_only']) ? count($data['data']['albums_only']) : 0;
            echo "<p class='info'>📊 Albums available: $album_count</p>";
            
            if ($album_count > 0) {
                echo "<p class='success'>✅ Data is ready for frontend</p>";
            } else {
                echo "<p class='warning'>⚠️ No albums in database</p>";
                echo "<p class='info'>Add some albums in the Admin Hub first</p>";
            }
        } else {
            echo "<p class='error'>❌ API error: " . htmlspecialchars($data['error'] ?? 'Unknown') . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
    }
} else {
    echo "<p class='error'>❌ API failed with HTTP $http_code</p>";
    echo "<p class='info'>cURL Error: " . htmlspecialchars($curl_error) . "</p>";
}

echo "</div>";

// Test 3: Create a JavaScript test
echo "<div class='test-section'>";
echo "<h3>🧪 Test 3: JavaScript Fetch Test</h3>";
echo "<p>This is the exact code your React app is running:</p>";
echo "<pre class='code'>
// This is what your Music.js component does:
const fetchMusicData = async () => {
    try {
        const response = await fetch('$api_url');
        const data = await response.json();
        
        if (data.success) {
            console.log('Albums:', data.data.albums_only);
            console.log('Singles:', data.data.singles);
        } else {
            console.error('API Error:', data.error);
        }
    } catch (err) {
        console.error('Fetch Error:', err);
    }
};
</pre>";

echo "<p><strong>Test this in your browser console:</strong></p>";
echo "<ol>";
echo "<li>Open your React app at <a href='http://127.0.0.1:3000' target='_blank'>http://127.0.0.1:3000</a></li>";
echo "<li>Open browser dev tools (F12)</li>";
echo "<li>Go to Console tab</li>";
echo "<li>Paste and run the code above</li>";
echo "<li>Check the results</li>";
echo "</ol>";
echo "</div>";

// Test 4: Common solutions
echo "<div class='test-section'>";
echo "<h3>🔧 Common Solutions</h3>";

echo "<h4>1. CORS Issues (Most Common)</h4>";
echo "<p>If React can't access the API due to CORS, add this to your React package.json:</p>";
echo "<pre class='code'>
\"proxy\": \"http://localhost/Portfolio/backend\"
</pre>";
echo "<p>Then in Music.js use: <code>fetch('/api/music.php')</code> instead of the full URL</p>";

echo "<h4>2. Network Issues</h4>";
echo "<p>Check if both servers are running:</p>";
echo "<ul>";
echo "<li>Apache/XAMPP for backend (port 80/443)</li>";
echo "<li>React dev server for frontend (port 3000)</li>";
echo "</ul>";

echo "<h4>3. URL Issues</h4>";
echo "<p>Try these API URLs in Music.js:</p>";
echo "<pre class='code'>
// Try these alternatives:
fetch('http://localhost/Portfolio/backend/api/music.php')
fetch('http://127.0.0.1/Portfolio/backend/api/music.php')
fetch('/api/music.php') // if proxy is set up
</pre>";

echo "</div>";

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>
            <h3>🎯 Immediate Actions:</h3>
            <ol>
                <li><strong>Check React console:</strong> Open F12 and look for network errors</li>
                <li><strong>Test API directly:</strong> <a href='../api/music.php' target='_blank'>Open API in browser</a></li>
                <li><strong>Add proxy if needed:</strong> Add proxy setting to package.json</li>
                <li><strong>Check servers:</strong> Make sure both Apache and React are running</li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 Test API Directly</a>
            <a href='http://127.0.0.1:3000' target='_blank' class='btn' style='background: #ffc107; color: #000;'>🚀 Open React App</a>
        </div>
    </div>
</body>
</html>";
?>
