<?php
// Emergency login bypass for testing
session_start();

echo "<h1>🚨 Emergency Login</h1>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    // Create or update admin user
    $username = 'admin';
    $password = 'admin123';
    
    $stmt = $connection->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Create admin user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connection->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);
        echo "✅ Admin user created<br>";
    } else {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "✅ Admin password updated<br>";
    }
    
    // Get user info
    $stmt = $connection->prepare("SELECT id, username FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        echo "<h2>✅ Emergency Login Successful!</h2>";
        echo "<p>Logged in as: <strong>" . htmlspecialchars($user['username']) . "</strong></p>";
        echo "<p>User ID: " . $user['id'] . "</p>";
        echo "<p>Login time: " . date('Y-m-d H:i:s') . "</p>";
        
        echo "<hr>";
        echo "<h3>🎯 Next Steps:</h3>";
        echo "<ol>";
        echo "<li><a href='dashboard.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📊 Go to Dashboard</a></li>";
        echo "<li><a href='settings.php' style='background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>⚙️ Change Password</a></li>";
        echo "</ol>";
        
        echo "<h3>🔐 Login Credentials:</h3>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> admin</li>";
        echo "<li><strong>Password:</strong> admin123</li>";
        echo "</ul>";
        
        echo "<p><small><strong>Note:</strong> This emergency login creates/updates the admin user. You can now login normally or change the password in settings.</small></p>";
        
    } else {
        echo "<h2>❌ Emergency Login Failed</h2>";
        echo "<p>Could not create or find admin user.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Emergency Login Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    echo "<h3>🔧 Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Check XAMPP MySQL is running</li>";
    echo "<li>Verify database exists</li>";
    echo "<li>Check file permissions</li>";
    echo "</ul>";
}
?>
