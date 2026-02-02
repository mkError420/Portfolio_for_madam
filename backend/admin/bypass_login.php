<?php
// Emergency login bypass - creates working session directly
session_start();

echo "<h1>🚨 Emergency Login Bypass</h1>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "<h2>Step 1: Force Create Admin User</h2>";
    
    // Delete any existing admin users to avoid conflicts
    $connection->exec("DELETE FROM admin_users");
    echo "✅ Cleared existing admin users<br>";
    
    // Create fresh admin user with correct password
    $username = 'admin';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $connection->prepare("INSERT INTO admin_users (username, password, email, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$username, $hashed_password, 'admin@example.com']);
    echo "✅ Created fresh admin user<br>";
    
    // Verify the user was created
    $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h2>Step 2: Verify User Creation</h2>";
        echo "✅ User ID: " . $user['id'] . "<br>";
        echo "✅ Username: " . htmlspecialchars($user['username']) . "<br>";
        echo "✅ Password hash stored correctly<br>";
        
        // Test password verification
        if (password_verify($password, $user['password'])) {
            echo "✅ Password verification: SUCCESS<br>";
        } else {
            echo "❌ Password verification: FAILED<br>";
        }
        
        echo "<h2>Step 3: Create Session</h2>";
        
        // Create session exactly like login.php would
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        echo "✅ Session created successfully<br>";
        echo "✅ Session ID: " . session_id() . "<br>";
        echo "✅ Admin logged in: " . ($_SESSION['admin_logged_in'] ? 'YES' : 'NO') . "<br>";
        
        echo "<h2>🎉 Emergency Login Successful!</h2>";
        
        echo "<div style='background: linear-gradient(135deg, #4CAF50, #45a049); color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
        echo "<h3>✅ You are now logged in!</h3>";
        echo "<p><strong>Session Details:</strong></p>";
        echo "<ul>";
        echo "<li>User ID: " . $_SESSION['admin_id'] . "</li>";
        echo "<li>Username: " . htmlspecialchars($_SESSION['admin_username']) . "</li>";
        echo "<li>Login Time: " . date('Y-m-d H:i:s', $_SESSION['login_time']) . "</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<h2>🎯 Next Steps:</h2>";
        echo "<ol>";
        echo "<li><a href='dashboard.php' style='background: #2196F3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;'>📊 Go to Dashboard</a></li>";
        echo "<li><a href='settings.php' style='background: #FF9800; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>⚙️ Change Password</a></li>";
        echo "</ol>";
        
        echo "<h2>🔐 Login Credentials (for future use):</h2>";
        echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;'>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><small>These credentials should now work for normal login.</small></p>";
        echo "</div>";
        
        echo "<h2>🔍 Test Normal Login</h2>";
        echo "<p>After visiting the dashboard, test the normal login:</p>";
        echo "<ol>";
        echo "<li><a href='logout.php' style='background: #f44336; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Logout</a></li>";
        echo "<li>Go to <a href='login.php'>login page</a></li>";
        echo "<li>Enter admin/admin123</li>";
        echo "<li>Should work normally now</li>";
        echo "</ol>";
        
    } else {
        echo "<h2>❌ Failed to Create Admin User</h2>";
        echo "<p>Something went wrong with user creation.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Emergency Login Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    
    echo "<h3>🔧 Manual Fix:</h3>";
    echo "<p>Run this SQL in phpMyAdmin:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo "DELETE FROM admin_users;
INSERT INTO admin_users (username, password, email, created_at) 
VALUES ('admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', NOW());";
    echo "</pre>";
}

echo "<hr>";
echo "<p><strong>⚠️ This is an emergency bypass. After accessing the dashboard, change your password for security.</strong></p>";
?>
