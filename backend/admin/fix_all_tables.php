<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

echo "<h2>Complete Database Structure Fix</h2>";

// Tables and their required columns
$tables_to_fix = [
    'albums' => [
        'artist' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER title",
        'release_date' => "DATE NULL AFTER artist",
        'genre' => "VARCHAR(100) DEFAULT '' AFTER release_date",
        'type' => "ENUM('album', 'single', 'ep') DEFAULT 'album' AFTER genre",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER type",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
    ],
    'videos' => [
        'category' => "VARCHAR(50) DEFAULT 'music_video' AFTER title",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER category",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
    ],
    'gallery' => [
        'category' => "VARCHAR(50) DEFAULT 'performance' AFTER title",
        'image_path' => "VARCHAR(255) NOT NULL AFTER category",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER image_path",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
    ],
    'tour_dates' => [
        'status' => "ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming' AFTER date",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
    ],
    'contact_messages' => [
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER message",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
    ]
];

foreach ($tables_to_fix as $table_name => $columns) {
    echo "<h3>Fixing Table: $table_name</h3>";
    
    try {
        // Check if table exists
        $result = $pdo->query("SHOW TABLES LIKE '$table_name'");
        if ($result->rowCount() == 0) {
            echo "<p style='color: orange;'>⚠️ Table $table_name doesn't exist, creating it...</p>";
            
            // Create table based on type
            switch ($table_name) {
                case 'albums':
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
                    break;
                    
                case 'videos':
                    $pdo->exec("CREATE TABLE videos (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) NOT NULL,
                        category VARCHAR(50) DEFAULT 'music_video',
                        url TEXT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )");
                    break;
                    
                case 'gallery':
                    $pdo->exec("CREATE TABLE gallery (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) NOT NULL,
                        category VARCHAR(50) DEFAULT 'performance',
                        image_path VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )");
                    break;
                    
                case 'tour_dates':
                    $pdo->exec("CREATE TABLE tour_dates (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        venue VARCHAR(255) NOT NULL,
                        city VARCHAR(255) NOT NULL,
                        date DATE NOT NULL,
                        status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )");
                    break;
                    
                case 'contact_messages':
                    $pdo->exec("CREATE TABLE contact_messages (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL,
                        message TEXT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )");
                    break;
            }
            
            echo "<p style='color: green;'>✅ Table $table_name created successfully</p>";
            continue;
        }
        
        // Get existing columns
        $result = $pdo->query("DESCRIBE $table_name");
        $existing_columns = array_column($result->fetchAll(PDO::FETCH_ASSOC), 'Field');
        
        // Add missing columns
        foreach ($columns as $column_name => $column_definition) {
            if (!in_array($column_name, $existing_columns)) {
                try {
                    $sql = "ALTER TABLE $table_name ADD COLUMN $column_name $column_definition";
                    $pdo->exec($sql);
                    echo "<p style='color: green;'>✅ Added column: $column_name</p>";
                } catch (PDOException $e) {
                    echo "<p style='color: red;'>❌ Error adding $column_name: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color: blue;'>ℹ️ Column $column_name already exists</p>";
            }
        }
        
        // Show final structure
        $result = $pdo->query("DESCRIBE $table_name");
        $columns_final = $result->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p><strong>Final structure:</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($columns_final as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error with table $table_name: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<h3>✅ All tables fixed!</h3>";
echo "<p><a href='admin_hub.php'>← Back to Admin Hub</a></p>";
?>
