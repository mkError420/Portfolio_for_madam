<?php
// Debug script to identify the exact issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Admin Login Debug</h1>";

echo "<h2>Step 1: PHP Extensions</h2>";
if (extension_loaded('pdo')) {
    echo "✅ PDO Extension: Loaded<br>";
} else {
    echo "❌ PDO Extension: Not Loaded<br>";
}

if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL: Loaded<br>";
} else {
    echo "❌ PDO MySQL: Not Loaded<br>";
}

echo "<h2>Step 2: MySQL Server Connection</h2>";
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ MySQL Server: Connected<br>";
    
    // Get MySQL version
    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "📊 MySQL Version: $version<br>";
    
} catch (PDOException $e) {
    echo "❌ MySQL Server: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 3: Database Check</h2>";
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $stmt = $pdo->query("SHOW DATABASES LIKE 'singer_portfolio'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database 'singer_portfolio': Exists<br>";
        
        // Connect to the database
        $pdo = new PDO('mysql:host=localhost;dbname=singer_portfolio', 'root', '');
        echo "✅ Database Connection: Success<br>";
        
        // Check tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "📋 Tables found: " . implode(', ', $tables) . "<br>";
        
        // Check admin_users table
        if (in_array('admin_users', $tables)) {
            echo "✅ admin_users table: Exists<br>";
            
            $count = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
            echo "👥 Admin users: $count<br>";
            
            if ($count > 0) {
                $admin = $pdo->query("SELECT username, created_at FROM admin_users LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                echo "👤 First admin: " . htmlspecialchars($admin['username']) . " (created: " . $admin['created_at'] . ")<br>";
            }
        } else {
            echo "❌ admin_users table: Missing<br>";
        }
        
    } else {
        echo "❌ Database 'singer_portfolio': Does not exist<br>";
    }
} catch (PDOException $e) {
    echo "❌ Database Check: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 4: File Permissions</h2>";
$config_file = '../config/database.php';
if (file_exists($config_file)) {
    echo "✅ database.php: Exists<br>";
    if (is_readable($config_file)) {
        echo "✅ database.php: Readable<br>";
    } else {
        echo "❌ database.php: Not readable<br>";
    }
} else {
    echo "❌ database.php: Missing<br>";
}

echo "<h2>Step 5: Test Login Script</h2>";
try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    echo "✅ Database Class: Working<br>";
    
    // Test admin login query
    $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Admin user found: " . htmlspecialchars($user['username']) . "<br>";
        if (password_verify('admin123', $user['password'])) {
            echo "✅ Password verification: Success<br>";
        } else {
            echo "❌ Password verification: Failed<br>";
        }
    } else {
        echo "❌ Admin user not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Login Test: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🔧 Quick Fix Options:</h2>";
echo "<ol>";
echo "<li><a href='setup.php'>Run Automatic Setup</a></li>";
echo "<li><a href='create_admin.php'>Create Admin User</a></li>";
echo "<li><a href='login.php'>Try Login Again</a></li>";
echo "</ol>";

echo "<h2>📋 Manual Setup Steps:</h2>";
echo "<ol>";
echo "<li>Open phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
echo "<li>Create database: 'singer_portfolio'</li>";
echo "<li>Import schema.sql file</li>";
echo "<li>Test login again</li>";
echo "</ol>";
?>
