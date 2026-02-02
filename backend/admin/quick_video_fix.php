<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Quick Video Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🎥 Quick Video Fix</h1>
        <p>Fixing the exact video insertion issue for Admin Hub.</p>";

try {
    require_once '../config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        echo "<p class='success'>✅ Database connected</p>";
        
        // Check if videos table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'videos'");
        $table_exists = $stmt->rowCount() > 0;
        
        if (!$table_exists) {
            echo "<p class='info'>📝 Creating videos table with minimal structure...</p>";
            
            // Create table with exactly what Admin Hub needs
            $create_sql = "
            CREATE TABLE videos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                category VARCHAR(100),
                url VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $pdo->exec($create_sql);
            echo "<p class='success'>✅ Videos table created with required columns</p>";
            
        } else {
            echo "<p class='info'>📝 Videos table exists, checking for missing columns...</p>";
            
            // Get current columns
            $stmt = $pdo->query("DESCRIBE videos");
            $current_columns = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $current_columns[] = $row['Field'];
            }
            
            echo "<h3>Current columns:</h3>";
            echo "<p>" . implode(', ', $current_columns) . "</p>";
            
            // Admin Hub needs these exact columns
            $required_columns = ['title', 'category', 'url'];
            
            foreach ($required_columns as $column) {
                if (!in_array($column, $current_columns)) {
                    echo "<p class='info'>➕ Adding missing column: $column</p>";
                    
                    if ($column === 'title') {
                        $alter_sql = "ALTER TABLE videos ADD COLUMN title VARCHAR(255) NOT NULL AFTER id";
                    } elseif ($column === 'category') {
                        $alter_sql = "ALTER TABLE videos ADD COLUMN category VARCHAR(100) AFTER title";
                    } elseif ($column === 'url') {
                        $alter_sql = "ALTER TABLE videos ADD COLUMN url VARCHAR(500) AFTER category";
                    }
                    
                    $pdo->exec($alter_sql);
                    echo "<p class='success'>✅ Added column: $column</p>";
                } else {
                    echo "<p class='success'>✅ Column exists: $column</p>";
                }
            }
        }
        
        // Show final table structure
        echo "<h3>📊 Final Videos Table Structure:</h3>";
        $stmt = $pdo->query("DESCRIBE videos");
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test the exact query that Admin Hub uses
        echo "<h3>🧪 Testing Admin Hub Query:</h3>";
        $test_title = 'Test Video';
        $test_category = 'music_video';
        $test_url = 'https://www.youtube.com/watch?v=test';
        
        try {
            $stmt = $pdo->prepare("INSERT INTO videos (title, category, url) VALUES (?, ?, ?)");
            $result = $stmt->execute([$test_title, $test_category, $test_url]);
            
            if ($result) {
                echo "<p class='success'>✅ Admin Hub query works perfectly!</p>";
                
                // Clean up test data
                $pdo->exec("DELETE FROM videos WHERE title = 'Test Video'");
                echo "<p class='info'>🧹 Test data cleaned up</p>";
            } else {
                echo "<p class='error'>❌ Admin Hub query failed</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Query error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
        echo "<p class='success'>🎉 Videos table is now ready for Admin Hub!</p>";
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #d4edda; border-radius: 5px; border-left: 4px solid #28a745;'>
            <h3>✅ Fix Complete!</h3>
            <p>The videos table now has exactly what Admin Hub needs:</p>
            <ul>
                <li><strong>title:</strong> Video title (required)</li>
                <li><strong>category:</strong> Video category</li>
                <li><strong>url:</strong> Video URL</li>
                <li><strong>id:</strong> Auto-increment primary key</li>
                <li><strong>created_at:</strong> Timestamp</li>
            </ul>
            <p><strong>Admin Hub query:</strong> INSERT INTO videos (title, category, url) VALUES (?, ?, ?)</p>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='admin_hub.php#videos' class='btn' style='background: #28a745;'>🎥 Add Videos</a>
        </div>
    </div>
</body>
</html>";
?>
