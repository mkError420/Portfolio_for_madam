<?php
// Check if frontend is running and provide correct URL
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Try to detect if React app is running on port 3000
$frontend_urls = [
    'http://localhost:3000',  // React development server
    'http://localhost:3001',  // Alternative port
    'http://127.0.0.1:3000',  // Localhost alternative
    'http://127.0.0.1:3001',  // Alternative port
    'http://localhost/Portfolio/frontend/public/index.html',  // Static fallback
];

$working_url = null;
foreach ($frontend_urls as $url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 2,
            'method' => 'HEAD'
        ]
    ]);
    
    if (@file_get_contents($url, false, $context) !== false) {
        $working_url = $url;
        break;
    }
}

// If no working URL found, default to React app
if (!$working_url) {
    $working_url = 'http://localhost:3000';
}

// Redirect to the working frontend
header("Location: $working_url");
exit();
?>
