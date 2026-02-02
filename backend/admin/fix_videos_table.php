<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Videos Table</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🎥 Fix Videos Table</h1>
        <p>Fixing the missing 'url' column in the videos table.</p>";

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
            echo "<p class='info'>📝 Creating videos table...</p>";
            
            $create_table_sql = "
            CREATE TABLE videos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                url VARCHAR(500),
                video_id VARCHAR(100),
                thumbnail VARCHAR(500),
                duration VARCHAR(20),
                views VARCHAR(50),
                category ENUM('music_video', 'live_performance', 'behind_scenes') DEFAULT 'music_video',
                release_date DATE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $pdo->exec($create_table_sql);
            echo "<p class='success'>✅ Videos table created</p>";
        } else {
            echo "<p class='info'>📝 Videos table exists, checking columns...</p>";
            
            // Get current columns
            $stmt = $pdo->query("DESCRIBE videos");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<h3>Current columns:</h3>";
            echo "<table>";
            echo "<tr><th>Column</th></tr>";
            foreach ($columns as $column) {
                echo "<tr><td>" . htmlspecialchars($column) . "</td></tr>";
            }
            echo "</table>";
            
            // Check and add missing columns
            $required_columns = [
                'url' => "VARCHAR(500)",
                'video_id' => "VARCHAR(100)",
                'thumbnail' => "VARCHAR(500)",
                'duration' => "VARCHAR(20)",
                'views' => "VARCHAR(50)",
                'category' => "ENUM('music_video', 'live_performance', 'behind_scenes') DEFAULT 'music_video'",
                'release_date' => "DATE"
            ];
            
            foreach ($required_columns as $column => $definition) {
                if (!in_array($column, $columns)) {
                    echo "<p class='info'>➕ Adding column: $column</p>";
                    $alter_sql = "ALTER TABLE videos ADD COLUMN $column $definition";
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
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test video insertion
        echo "<h3>🧪 Test Video Insertion:</h3>";
        $test_video = [
            'title' => 'Test Video',
            'description' => 'This is a test video',
            'url' => 'https://www.youtube.com/watch?v=test',
            'video_id' => 'test',
            'thumbnail' => 'https://via.placeholder.com/300x200/2a2a2a/ffffff?text=Test',
            'duration' => '3:30',
            'views' => '1K',
            'category' => 'music_video',
            'release_date' => date('Y-m-d')
        ];
        
        $stmt = $pdo->prepare("INSERT INTO videos (title, description, url, video_id, thumbnail, duration, views, category, release_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([
            $test_video['title'],
            $test_video['description'],
            $test_video['url'],
            $test_video['video_id'],
            $test_video['thumbnail'],
            $test_video['duration'],
            $test_video['views'],
            $test_video['category'],
            $test_video['release_date']
        ])) {
            echo "<p class='success'>✅ Test video inserted successfully!</p>";
            
            // Remove test video
            $pdo->exec("DELETE FROM videos WHERE title = 'Test Video'");
            echo "<p class='info'>🧹 Test video removed</p>";
        } else {
            echo "<p class='error'>❌ Test video insertion failed</p>";
        }
        
        echo "<p class='success'>🎉 Videos table is now ready!</p>";
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #d4edda; border-radius: 5px; border-left: 4px solid #28a745;'>
            <h3>✅ Fix Complete!</h3>
            <p>The videos table now has all required columns:</p>
            <ul>
                <li><strong>url:</strong> Video URL</li>
                <li><strong>video_id:</strong> YouTube video ID</li>
                <li><strong>thumbnail:</strong> Thumbnail image URL</li>
                <li><strong>duration:</strong> Video duration</li>
                <li><strong>views:</strong> View count</li>
                <li><strong>category:</strong> Video category</li>
                <li><strong>release_date:</strong> Release date</li>
            </ul>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='admin_hub.php#videos' class='btn' style='background: #28a745;'>🎥 Add Videos</a>
        </div>
    </div>
</body>
</html>";
?>
