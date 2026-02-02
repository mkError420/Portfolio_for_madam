<?php
// Quick fix for missing password column
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚨 Quick Database Fix</h1>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "<h2>Step 1: Check Current Table Structure</h2>";
    
    // Get current columns
    $columns = $connection->query("DESCRIBE admin_users")->fetchAll(PDO::FETCH_ASSOC);
    $column_names = array_column($columns, 'Field');
    
    echo "<p>Current columns: " . implode(', ', $column_names) . "</p>";
    
    // Check what's missing
    $missing_columns = [];
    $required_columns = ['password', 'email', 'created_at'];
    
    foreach ($required_columns as $col) {
        if (!in_array($col, $column_names)) {
            $missing_columns[] = $col;
        }
    }
    
    if (!empty($missing_columns)) {
        echo "<h2>Step 2: Add Missing Columns</h2>";
        
        foreach ($missing_columns as $column) {
            try {
                switch ($column) {
                    case 'password':
                        $connection->exec("ALTER TABLE admin_users ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '' AFTER username");
                        echo "✅ Added 'password' column<br>";
                        break;
                    case 'email':
                        $connection->exec("ALTER TABLE admin_users ADD COLUMN email VARCHAR(255) DEFAULT '' AFTER password");
                        echo "✅ Added 'email' column<br>";
                        break;
                    case 'created_at':
                        $connection->exec("ALTER TABLE admin_users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
                        echo "✅ Added 'created_at' column<br>";
                        break;
                }
            } catch (Exception $e) {
                echo "❌ Failed to add '$column' column: " . $e->getMessage() . "<br>";
            }
        }
    } else {
        echo "<h2>✅ All Required Columns Exist</h2>";
    }
    
    echo "<h2>Step 3: Set Admin Password</h2>";
    
    // Hash the password
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Update admin user
    $stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
    $stmt->execute([$hashed_password, 'admin']);
    
    echo "✅ Admin password set to 'admin123'<br>";
    
    echo "<h2>Step 4: Test Login</h2>";
    
    // Test the login
    $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify('admin123', $user['password'])) {
        echo "✅ Login test: SUCCESS<br>";
        echo "✅ User ID: " . $user['id'] . "<br>";
        echo "✅ Username: " . htmlspecialchars($user['username']) . "<br>";
        
        echo "<h2>🎉 Fix Complete!</h2>";
        echo "<div style='background: #4CAF50; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
        echo "<h3>✅ Ready to Login!</h3>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "</div>";
        
        echo "<p><a href='login.php' style='background: #2196F3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>🔐 Go to Login Now</a></p>";
        
    } else {
        echo "❌ Login test still failing<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Fix Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    
    echo "<h3>🔧 Manual SQL Fix:</h3>";
    echo "<p>If this script fails, run these SQL commands in phpMyAdmin:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo "ALTER TABLE admin_users ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '' AFTER username;
ALTER TABLE admin_users ADD COLUMN email VARCHAR(255) DEFAULT '' AFTER password;
ALTER TABLE admin_users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
UPDATE admin_users SET password = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';";
    echo "</pre>";
}

echo "<hr>";
echo "<p><small>This fix adds the missing database columns required for the login system to work properly.</small></p>";
?>
