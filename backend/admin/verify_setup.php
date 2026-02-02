<?php
// Verify existing database setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>✅ Database Verification</h1>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "<h2>🎉 Database Connection: SUCCESS!</h2>";
    
    // Check all tables
    $tables = $connection->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>📋 Tables Found:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>✅ $table</li>";
    }
    echo "</ul>";
    
    // Check admin users
    $stmt = $connection->query("SELECT COUNT(*) as count FROM admin_users");
    $admin_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<h3>👥 Admin Users: $admin_count</h3>";
    
    if ($admin_count > 0) {
        $stmt = $connection->query("SELECT username, created_at FROM admin_users");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>Admin Accounts:</h4>";
        echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
        echo "<tr><th>Username</th><th>Created</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . $admin['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test password for admin user
        $stmt = $connection->prepare("SELECT password FROM admin_users WHERE username = ?");
        $stmt->execute(['admin']);
        $admin_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin_data && password_verify('admin123', $admin_data['password'])) {
            echo "<h3 style='color: green;'>✅ Password Verification: PASSED</h3>";
            echo "<p>You can login with:</p>";
            echo "<ul>";
            echo "<li><strong>Username:</strong> admin</li>";
            echo "<li><strong>Password:</strong> admin123</li>";
            echo "</ul>";
            echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login</a></p>";
        } else {
            echo "<h3 style='color: orange;'>⚠️ Password Issue Detected</h3>";
            echo "<p>The admin user exists but the password might be different.</p>";
            echo "<p><a href='create_admin.php' style='background: #ff9800; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 Reset Admin Password</a></p>";
        }
    } else {
        echo "<h3 style='color: red;'>❌ No Admin Users Found</h3>";
        echo "<p>Creating admin user...</p>";
        
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $connection->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashed_password]);
        
        echo "<p style='color: green;'>✅ Admin user created!</p>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>Password: <strong>admin123</strong></p>";
        echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login</a></p>";
    }
    
    // Check sample data
    echo "<h3>📊 Sample Data Check:</h3>";
    $sample_data = [
        'albums' => 'SELECT COUNT(*) as count FROM albums',
        'tracks' => 'SELECT COUNT(*) as count FROM tracks', 
        'singles' => 'SELECT COUNT(*) as count FROM singles',
        'videos' => 'SELECT COUNT(*) as count FROM videos',
        'gallery' => 'SELECT COUNT(*) as count FROM gallery',
        'tour_dates' => 'SELECT COUNT(*) as count FROM tour_dates'
    ];
    
    foreach ($sample_data as $table => $query) {
        if (in_array($table, $tables)) {
            $count = $connection->query($query)->fetch(PDO::FETCH_ASSOC)['count'];
            echo "<li>$table: $count records</li>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    echo "<h3>🔧 Possible Solutions:</h3>";
    echo "<ol>";
    echo "<li>Check XAMPP MySQL is running</li>";
    echo "<li>Verify database name is 'singer_portfolio'</li>";
    echo "<li>Check MySQL username/password (default: root/empty)</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><a href='login.php'>← Back to Login</a> | <a href='debug.php'>Debug Info</a> | <a href='create_admin.php'>Manage Admin</a></p>";
?>
