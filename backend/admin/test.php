<?php
// Simple test file to verify PHP is working
echo "<h1>Backend Admin Panel - Test Page</h1>";
echo "<p>PHP is working correctly!</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Not set' . "</p>";
echo "<p>Server name: " . $_SERVER['SERVER_NAME'] ?? 'Not set' . "</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

// Test database connection
try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Available Admin Pages:</h3>";
echo "<ul>";
echo "<li><a href='login.php'>🔐 Login Page</a></li>";
echo "<li><a href='dashboard.php'>📊 Dashboard</a> (requires login)</li>";
echo "<li><a href='music.php'>🎵 Music Management</a> (requires login)</li>";
echo "<li><a href='videos.php'>🎬 Video Management</a> (requires login)</li>";
echo "<li><a href='gallery.php'>🖼️ Gallery Management</a> (requires login)</li>";
echo "<li><a href='tour.php'>📅 Tour Management</a> (requires login)</li>";
echo "<li><a href='messages.php'>📧 Message Management</a> (requires login)</li>";
echo "<li><a href='settings.php'>⚙️ Settings</a> (requires login)</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Quick Setup:</h3>";
echo "<ol>";
echo "<li>First, test the <a href='login.php'>login page</a></li>";
echo "<li>Default credentials: admin / admin123</li>";
echo "<li>If this page works, but others don't, you need Apache configuration</li>";
echo "</ol>";
?>
