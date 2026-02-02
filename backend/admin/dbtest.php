<?php
// Database connection test
echo "<h1>Database Connection Test</h1>";

// Test 1: Check if MySQL extension is loaded
if (!extension_loaded('pdo_mysql')) {
    echo "<p style='color: red;'>❌ PDO MySQL extension is not loaded!</p>";
    echo "<p>Please enable pdo_mysql in your php.ini file</p>";
} else {
    echo "<p style='color: green;'>✅ PDO MySQL extension is loaded</p>";
}

// Test 2: Try to connect to MySQL server
try {
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
    echo "<p style='color: green;'>✅ Can connect to MySQL server</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Cannot connect to MySQL server: " . $e->getMessage() . "</p>";
}

// Test 3: Check if our database exists
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $stmt = $pdo->query("SHOW DATABASES LIKE 'singer_portfolio'");
    $db_exists = $stmt->rowCount() > 0;
    
    if ($db_exists) {
        echo "<p style='color: green;'>✅ Database 'singer_portfolio' exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Database 'singer_portfolio' does not exist</p>";
        echo "<p>Please import the database schema first</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error checking database: " . $e->getMessage() . "</p>";
}

// Test 4: Check our database configuration
echo "<h3>Current Database Configuration:</h3>";
echo "<p><strong>Host:</strong> localhost</p>";
echo "<p><strong>Database:</strong> singer_portfolio</p>";
echo "<p><strong>Username:</strong> root</p>";
echo "<p><strong>Password:</strong> (empty)</p>";

// Test 5: Try our actual database connection
echo "<h3>Testing Actual Database Connection:</h3>";
try {
    require_once '../config/database.php';
    $db = new Database();
    $connection = $db->getConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test if admin_users table exists
    $stmt = $connection->query("SHOW TABLES LIKE 'admin_users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ admin_users table exists</p>";
        
        // Check if admin user exists
        $stmt = $connection->query("SELECT COUNT(*) as count FROM admin_users");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Admin users found: " . $count . "</p>";
    } else {
        echo "<p style='color: red;'>❌ admin_users table does not exist</p>";
        echo "<p>Please import the database schema</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Setup Steps:</h3>";
echo "<ol>";
echo "<li>Make sure XAMPP MySQL is running</li>";
echo "<li>Open phpMyAdmin: http://localhost/phpmyadmin</li>";
echo "<li>Create database named 'singer_portfolio'</li>";
echo "<li>Import the file: database/schema.sql</li>";
echo "<li>Test the login again</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='login.php'>← Back to Login</a></p>";
?>
