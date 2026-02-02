<?php
// Automatic setup script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 Automatic Database Setup</h1>";

try {
    // Step 1: Connect to MySQL
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to MySQL server<br>";

    // Step 2: Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS singer_portfolio");
    $pdo->exec("USE singer_portfolio");
    echo "✅ Database 'singer_portfolio' ready<br>";

    // Step 3: Read and execute schema
    $schema_file = '../database/schema.sql';
    if (file_exists($schema_file)) {
        $schema = file_get_contents($schema_file);
        
        // Remove the CREATE DATABASE and USE statements since we're already connected
        $schema = preg_replace('/^CREATE DATABASE.*?;\s*USE.*?;\s*/m', '', $schema);
        
        // Split into individual statements
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        echo "✅ Database schema imported<br>";
    } else {
        echo "❌ Schema file not found<br>";
    }

    // Step 4: Create admin user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashed_password]);
        echo "✅ Admin user created (username: admin, password: admin123)<br>";
    } else {
        echo "✅ Admin user already exists<br>";
    }

    // Step 5: Verify setup
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tables created: " . implode(', ', $tables) . "<br>";

    $admin_count = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    echo "✅ Admin users: $admin_count<br>";

    echo "<hr>";
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='login.php'>Go to Login Page</a></li>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "<li>Change password after first login!</li>";
    echo "</ol>";

} catch (Exception $e) {
    echo "<h2>❌ Setup Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check that MySQL username is 'root' with no password</li>";
    echo "<li>Verify file permissions</li>";
    echo "</ul>";
}
?>
