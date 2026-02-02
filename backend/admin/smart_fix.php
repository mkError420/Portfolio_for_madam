<?php
// Smart fix that only adds missing columns
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Smart Database Fix</h1>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "<h2>Step 1: Check Current Columns</h2>";
    
    // Get current columns
    $columns_result = $connection->query("DESCRIBE admin_users")->fetchAll(PDO::FETCH_ASSOC);
    $current_columns = array_column($columns_result, 'Field');
    
    echo "<p>Current columns: " . implode(', ', $current_columns) . "</p>";
    
    // Required columns and their definitions
    $required_columns = [
        'password' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'email' => "VARCHAR(255) DEFAULT ''",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ];
    
    echo "<h2>Step 2: Add Only Missing Columns</h2>";
    
    $added_columns = [];
    foreach ($required_columns as $column_name => $column_def) {
        if (!in_array($column_name, $current_columns)) {
            try {
                // Determine position for the column
                $after_clause = '';
                if ($column_name === 'password') {
                    $after_clause = 'AFTER username';
                } elseif ($column_name === 'email') {
                    if (in_array('password', $current_columns)) {
                        $after_clause = 'AFTER password';
                    } else {
                        $after_clause = 'AFTER username';
                    }
                }
                
                $sql = "ALTER TABLE admin_users ADD COLUMN $column_name $column_def $after_clause";
                $connection->exec($sql);
                echo "✅ Added '$column_name' column<br>";
                $added_columns[] = $column_name;
            } catch (Exception $e) {
                echo "❌ Failed to add '$column_name': " . $e->getMessage() . "<br>";
            }
        } else {
            echo "✅ '$column_name' column already exists<br>";
        }
    }
    
    echo "<h2>Step 3: Update Admin Password</h2>";
    
    // Check if password column exists and has data
    if (in_array('password', $current_columns) || in_array('password', $added_columns)) {
        // Check if admin user has a password
        $stmt = $connection->prepare("SELECT password FROM admin_users WHERE username = ?");
        $stmt->execute(['admin']);
        $current_password = $stmt->fetchColumn();
        
        if (empty($current_password) || $current_password === '') {
            // Set the password
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $update_stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $update_stmt->execute([$hashed_password, 'admin']);
            echo "✅ Admin password set to 'admin123'<br>";
        } else {
            // Test if current password works
            if (password_verify('admin123', $current_password)) {
                echo "✅ Admin password already set correctly<br>";
            } else {
                // Update to correct password
                $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
                $update_stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
                $update_stmt->execute([$hashed_password, 'admin']);
                echo "✅ Admin password updated to 'admin123'<br>";
            }
        }
    } else {
        echo "❌ Password column still missing<br>";
    }
    
    echo "<h2>Step 4: Final Test</h2>";
    
    // Test the complete login process
    try {
        $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
        $stmt->execute(['admin']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify('admin123', $user['password'])) {
            echo "✅ Login test: SUCCESS<br>";
            echo "✅ User ID: " . $user['id'] . "<br>";
            echo "✅ Username: " . htmlspecialchars($user['username']) . "<br>";
            
            echo "<div style='background: linear-gradient(135deg, #4CAF50, #45a049); color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
            echo "<h2>🎉 Database Fixed Successfully!</h2>";
            echo "<p><strong>Login Credentials:</strong></p>";
            echo "<ul>";
            echo "<li>Username: <strong>admin</strong></li>";
            echo "<li>Password: <strong>admin123</strong></li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<p><a href='login.php' style='background: #2196F3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold;'>🔐 Login to Dashboard</a></p>";
            
        } else {
            echo "❌ Login test failed<br>";
            if (!$user) {
                echo "❌ Admin user not found<br>";
            } else {
                echo "❌ Password verification failed<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Login test error: " . $e->getMessage() . "<br>";
    }
    
    // Show final table structure
    echo "<h2>Final Table Structure:</h2>";
    $final_columns = $connection->query("DESCRIBE admin_users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($final_columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h2>❌ Smart Fix Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    echo "<h3>🔧 Manual Fix Options:</h3>";
    echo "<ol>";
    echo "<li>Check which columns are missing in phpMyAdmin</li>";
    echo "<li>Add only the missing columns manually</li>";
    echo "<li>Set the admin password hash: \$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><strong>✨ This smart fix only adds columns that are actually missing, avoiding duplicate column errors.</strong></p>";
?>
