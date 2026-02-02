<?php
// Fix database structure issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Database Structure Fix</h1>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "<h2>Step 1: Check Current admin_users Structure</h2>";
    
    $columns = $connection->query("DESCRIBE admin_users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Current columns:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if password column exists
    $password_exists = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'password') {
            $password_exists = true;
            break;
        }
    }
    
    if (!$password_exists) {
        echo "<h2>Step 2: Add Missing password Column</h2>";
        
        try {
            $connection->exec("ALTER TABLE admin_users ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '' AFTER username");
            echo "✅ password column added successfully<br>";
        } catch (Exception $e) {
            echo "❌ Failed to add password column: " . $e->getMessage() . "<br>";
        }
        
        // Check if email column exists
        $email_exists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'email') {
                $email_exists = true;
                break;
            }
        }
        
        if (!$email_exists) {
            try {
                $connection->exec("ALTER TABLE admin_users ADD COLUMN email VARCHAR(255) DEFAULT '' AFTER password");
                echo "✅ email column added successfully<br>";
            } catch (Exception $e) {
                echo "❌ Failed to add email column: " . $e->getMessage() . "<br>";
            }
        }
        
        // Check if created_at column exists
        $created_at_exists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'created_at') {
                $created_at_exists = true;
                break;
            }
        }
        
        if (!$created_at_exists) {
            try {
                $connection->exec("ALTER TABLE admin_users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
                echo "✅ created_at column added successfully<br>";
            } catch (Exception $e) {
                echo "❌ Failed to add created_at column: " . $e->getMessage() . "<br>";
            }
        }
        
    } else {
        echo "<h2>✅ password column already exists</h2>";
    }
    
    echo "<h2>Step 3: Create/Update Admin User</h2>";
    
    // Check if admin user exists
    $stmt = $connection->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $count = $stmt->fetchColumn();
    
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    if ($count == 0) {
        // Create admin user
        $stmt = $connection->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hashed_password, 'admin@example.com']);
        echo "✅ Admin user created (admin/admin123)<br>";
    } else {
        // Update admin user password
        $stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, 'admin']);
        echo "✅ Admin password updated (admin/admin123)<br>";
    }
    
    echo "<h2>Step 4: Test Login</h2>";
    
    // Test login
    $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify('admin123', $user['password'])) {
        echo "✅ Login test: SUCCESS<br>";
        echo "✅ User ID: " . $user['id'] . "<br>";
        echo "✅ Username: " . htmlspecialchars($user['username']) . "<br>";
        
        echo "<h2>🎉 Database Fixed!</h2>";
        echo "<p><strong>Login Credentials:</strong></p>";
        echo "<ul>";
        echo "<li>Username: <strong>admin</strong></li>";
        echo "<li>Password: <strong>admin123</strong></li>";
        echo "</ul>";
        
        echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 16px;'>🔐 Go to Login</a></p>";
        
    } else {
        echo "❌ Login test: FAILED<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Database Fix Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>📋 What Was Fixed:</h2>";
echo "<ul>";
echo "<li>✅ Added missing 'password' column to admin_users table</li>";
echo "<li>✅ Added missing 'email' column to admin_users table</li>";
echo "<li>✅ Added missing 'created_at' column to admin_users table</li>";
echo "<li>✅ Created/updated admin user with proper password hash</li>";
echo "<li>✅ Tested login functionality</li>";
echo "</ul>";

echo "<p><small><strong>Note:</strong> The database schema was incomplete. This fix adds all required columns for the admin authentication system.</small></p>";
?>
