<?php
// Comprehensive login debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Login Debug Tool</h1>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "<h2>Step 1: Check admin_users Table</h2>";
    
    // Get all admin users
    $stmt = $connection->query("SELECT * FROM admin_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>All Admin Users:</h3>";
    if (empty($users)) {
        echo "<p style='color: red;'>❌ No admin users found in database!</p>";
        
        // Create admin user
        echo "<h3>Creating Admin User...</h3>";
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $connection->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hashed_password, 'admin@example.com']);
        echo "<p style='color: green;'>✅ Admin user created (admin/admin123)</p>";
        
        // Get the created user
        $stmt = $connection->query("SELECT * FROM admin_users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Username</th><th>Password Hash</th><th>Email</th><th>Created</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td style='font-family: monospace; font-size: 12px;'>" . htmlspecialchars($user['password']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
        echo "<td>" . ($user['created_at'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Step 2: Test Password Verification</h2>";
    
    foreach ($users as $user) {
        echo "<h4>Testing user: " . htmlspecialchars($user['username']) . "</h4>";
        
        if (password_verify('admin123', $user['password'])) {
            echo "<p style='color: green;'>✅ Password 'admin123' works for this user</p>";
        } else {
            echo "<p style='color: red;'>❌ Password 'admin123' FAILED for this user</p>";
            
            // Update the password
            echo "<p>Updating password for this user...</p>";
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $update_stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $update_stmt->execute([$new_hash, $user['id']]);
            echo "<p style='color: green;'>✅ Password updated for " . htmlspecialchars($user['username']) . "</p>";
            
            // Test again
            if (password_verify('admin123', $new_hash)) {
                echo "<p style='color: green;'>✅ Password verification now works!</p>";
            }
        }
    }
    
    echo "<h2>Step 3: Simulate Login Process</h2>";
    
    $username = 'admin';
    $password = 'admin123';
    
    echo "<p>Testing login with:</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> " . htmlspecialchars($username) . "</li>";
    echo "<li><strong>Password:</strong> " . htmlspecialchars($password) . "</li>";
    echo "</ul>";
    
    // Exact same logic as login.php
    if (empty($username) || empty($password)) {
        echo "<p style='color: red;'>❌ Empty credentials</p>";
    } else {
        try {
            $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<p>✅ User found in database</p>";
                echo "<p>User ID: " . $user['id'] . "</p>";
                echo "<p>Username: " . htmlspecialchars($user['username']) . "</p>";
                
                if (password_verify($password, $user['password'])) {
                    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ LOGIN SUCCESSFUL!</p>";
                    
                    // Show session data that would be created
                    echo "<h3>Session Data (would be created):</h3>";
                    echo "<ul>";
                    echo "<li>admin_logged_in: true</li>";
                    echo "<li>admin_id: " . $user['id'] . "</li>";
                    echo "<li>admin_username: " . htmlspecialchars($user['username']) . "</li>";
                    echo "<li>login_time: " . time() . "</li>";
                    echo "</ul>";
                    
                    echo "<div style='background: #4CAF50; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
                    echo "<h2>🎉 Login is Working!</h2>";
                    echo "<p>The login should now work. Try logging in again.</p>";
                    echo "<p><a href='login.php' style='background: white; color: #4CAF50; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔐 Try Login Now</a></p>";
                    echo "</div>";
                    
                } else {
                    echo "<p style='color: red;'>❌ Password verification failed</p>";
                    echo "<p>This shouldn't happen after the password update above.</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ User not found in database</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Login query error: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2>Step 4: Test Different Passwords</h2>";
    
    $test_passwords = ['admin123', 'admin', 'password', '123456'];
    foreach ($users as $user) {
        echo "<h4>Testing passwords for " . htmlspecialchars($user['username']) . ":</h4>";
        foreach ($test_passwords as $test_pass) {
            if (password_verify($test_pass, $user['password'])) {
                echo "<p style='color: green;'>✅ '$test_pass' works!</p>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Debug Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🔧 Quick Actions:</h2>";
echo "<ul>";
echo "<li><a href='login.php'>🔐 Try Login Again</a></li>";
echo "<li><a href='smart_fix.php'>🔧 Run Smart Fix</a></li>";
echo "<li><a href='emergency_login.php'>🚨 Emergency Login</a></li>";
echo "</ul>";

echo "<p><small>This tool shows exactly what's in your database and tests the login process step by step.</small></p>";
?>
