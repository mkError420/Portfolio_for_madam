<?php
// Comprehensive login fix script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Login Fix Tool</h1>";

echo "<h2>Step 1: Test Database Connection Directly</h2>";

// Test 1: Basic PDO connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=singer_portfolio', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Direct PDO Connection: SUCCESS<br>";
} catch (PDOException $e) {
    echo "❌ Direct PDO Connection: " . $e->getMessage() . "<br>";
    
    // Try without database name
    try {
        $pdo = new PDO('mysql:host=localhost', 'root', '');
        echo "✅ MySQL Server Connection: SUCCESS (but database issue)<br>";
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE 'singer_portfolio'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Database exists<br>";
        } else {
            echo "❌ Database 'singer_portfolio' does not exist<br>";
            echo "<p><a href='setup.php'>Create Database</a></p>";
        }
    } catch (PDOException $e2) {
        echo "❌ MySQL Server Connection: " . $e2->getMessage() . "<br>";
        echo "<p><strong>XAMPP MySQL might not be running!</strong></p>";
    }
}

echo "<h2>Step 2: Test Database Class</h2>";

try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    echo "✅ Database Class: SUCCESS<br>";
    
    // Test admin_users table
    $stmt = $connection->query("SELECT COUNT(*) FROM admin_users");
    $count = $stmt->fetchColumn();
    echo "✅ Admin users table: $count users<br>";
    
    if ($count == 0) {
        echo "⚠️ No admin users found - creating one...<br>";
        
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $connection->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashed_password]);
        echo "✅ Admin user created (admin/admin123)<br>";
    }
    
    // Test login query
    $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Admin user found: " . htmlspecialchars($user['username']) . "<br>";
        
        if (password_verify('admin123', $user['password'])) {
            echo "✅ Password verification: SUCCESS<br>";
        } else {
            echo "❌ Password verification: FAILED<br>";
            echo "Updating password...<br>";
            
            $new_password = password_hash('admin123', PASSWORD_DEFAULT);
            $update_stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $update_stmt->execute([$new_password, 'admin']);
            echo "✅ Password updated<br>";
        }
    } else {
        echo "❌ Admin user not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database Class Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 3: Test Actual Login Process</h2>";

// Simulate login process
try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    
    $username = 'admin';
    $password = 'admin123';
    
    if (empty($username) || empty($password)) {
        echo "❌ Empty credentials<br>";
    } else {
        $stmt = $connection->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            echo "✅ Login simulation: SUCCESS<br>";
            echo "✅ Session would be created for user ID: " . $user['id'] . "<br>";
        } else {
            echo "❌ Login simulation: FAILED<br>";
            echo "❌ Invalid credentials<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Login simulation error: " . $e->getMessage() . "<br>";
}

echo "<h2>🔧 Quick Fixes:</h2>";

// Check if we can create a simple login bypass
echo "<h3>Option 1: Emergency Login</h3>";
echo "<p>If everything else fails, try this emergency login:</p>";
echo "<form method='POST' action='emergency_login.php'>";
echo "<input type='hidden' name='username' value='admin'>";
echo "<input type='hidden' name='password' value='admin123'>";
echo "<button type='submit' style='background: #ff5722; color: white; padding: 10px 20px; border: none; border-radius: 5px;'>🚨 Emergency Login</button>";
echo "</form>";

echo "<h3>Option 2: Reset Everything</h3>";
echo "<p><a href='setup.php' style='background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 Run Full Setup</a></p>";

echo "<h3>Option 3: Manual Database Check</h3>";
echo "<p><a href='verify_setup.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔍 Verify Setup</a></p>";

echo "<hr>";
echo "<h2>📋 Common Issues & Solutions:</h2>";
echo "<ul>";
echo "<li><strong>XAMPP MySQL not running</strong>: Start MySQL in XAMPP Control Panel</li>";
echo "<li><strong>Wrong database name</strong>: Should be 'singer_portfolio'</li>";
echo "<li><strong>Wrong MySQL credentials</strong>: Should be root/empty password</li>";
echo "<li><strong>Missing admin user</strong>: Run the setup script</li>";
echo "<li><strong>Corrupted database</strong>: Drop and recreate database</li>";
echo "</ul>";
?>
