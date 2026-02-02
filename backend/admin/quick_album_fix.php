<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Quick Album Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Quick Album Database Fix</h1>
        <p>This will fix the 'artist column not found' error immediately.</p>";

try {
    echo "<h2>📀 Fixing Albums Table...</h2>";
    
    // Check if table exists
    $result = $pdo->query("SHOW TABLES LIKE 'albums'");
    if ($result->rowCount() == 0) {
        echo "<p class='info'>Creating albums table...</p>";
        $pdo->exec("CREATE TABLE albums (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            artist VARCHAR(255) NOT NULL DEFAULT '',
            release_date DATE NULL,
            genre VARCHAR(100) DEFAULT '',
            type ENUM('album', 'single', 'ep') DEFAULT 'album',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        echo "<p class='success'>✅ Albums table created successfully!</p>";
    } else {
        echo "<p class='info'>Albums table exists, checking columns...</p>";
        
        // Get existing columns
        $result = $pdo->query("DESCRIBE albums");
        $existing_columns = array_column($result->fetchAll(PDO::FETCH_ASSOC), 'Field');
        
        echo "<p><strong>Current columns:</strong> " . implode(', ', $existing_columns) . "</p>";
        
        // Add missing columns one by one
        $columns_to_add = [
            'artist' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER title",
            'release_date' => "DATE NULL AFTER artist",
            'genre' => "VARCHAR(100) DEFAULT '' AFTER release_date",
            'type' => "ENUM('album', 'single', 'ep') DEFAULT 'album' AFTER genre",
            'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER type",
            'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
        ];
        
        foreach ($columns_to_add as $column_name => $column_definition) {
            if (!in_array($column_name, $existing_columns)) {
                try {
                    $sql = "ALTER TABLE albums ADD COLUMN $column_name $column_definition";
                    $pdo->exec($sql);
                    echo "<p class='success'>✅ Added column: $column_name</p>";
                } catch (PDOException $e) {
                    echo "<p class='error'>❌ Error adding $column_name: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p class='info'>ℹ️ Column $column_name already exists</p>";
            }
        }
    }
    
    // Show final structure
    echo "<h3>📋 Final Albums Table Structure:</h3>";
    $result = $pdo->query("DESCRIBE albums");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Column</th><th>Type</th><th>Nullable</th><th>Key</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($column['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test the insert that was failing
    echo "<h3>🧪 Testing Album Insert (This was failing before):</h3>";
    try {
        $stmt = $pdo->prepare("INSERT INTO albums (title, artist, release_date, genre, type) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute(['Test Album', 'Test Artist', '2024-01-01', 'Pop', 'album']);
        
        if ($result) {
            echo "<p class='success'>✅ SUCCESS: Test album added without errors!</p>";
            
            // Clean up test data
            $pdo->exec("DELETE FROM albums WHERE title = 'Test Album' AND artist = 'Test Artist'");
            echo "<p class='info'>ℹ️ Test data cleaned up</p>";
            
            echo "<h2 class='success'>🎉 FIX COMPLETE!</h2>";
            echo "<p><strong>The 'artist column not found' error is now FIXED!</strong></p>";
            
        } else {
            echo "<p class='error'>❌ Insert test failed</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Test insert failed: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px; border-left: 4px solid #007bff;'>
            <h3>🎯 Next Steps:</h3>
            <ol>
                <li><strong>Go to Admin Hub:</strong> <a href='admin_hub.php' class='btn'>Admin Hub</a></li>
                <li><strong>Navigate to Music section</strong></li>
                <li><strong>Fill in the album form</strong></li>
                <li><strong>Click 'Add Album/Single'</strong></li>
                <li><strong>It should work perfectly now! 🎵</strong></li>
            </ol>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🚀 Go to Admin Hub</a>
            <a href='login.php' class='btn' style='background: #6c757d;'>🔐 Login Page</a>
        </div>
    </div>
</body>
</html>";
?>
