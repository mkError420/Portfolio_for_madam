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
    <title>Auto Database Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        table { border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔧 Auto Database Fix</h1>";

try {
    echo "<h2>📀 Fixing Albums Table</h2>";
    
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
        echo "<p class='success'>✅ Albums table created</p>";
    } else {
        // Get existing columns
        $result = $pdo->query("DESCRIBE albums");
        $existing_columns = array_column($result->fetchAll(PDO::FETCH_ASSOC), 'Field');
        
        // Columns to add
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
    echo "<h3>Final Albums Table Structure:</h3>";
    $result = $pdo->query("DESCRIBE albums");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test insert
    echo "<h3>Testing Album Insert:</h3>";
    try {
        $stmt = $pdo->prepare("INSERT INTO albums (title, artist, release_date, genre, type) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute(['Test Album', 'Test Artist', '2024-01-01', 'Pop', 'album']);
        
        if ($result) {
            echo "<p class='success'>✅ Test album added successfully!</p>";
            $pdo->exec("DELETE FROM albums WHERE title = 'Test Album' AND artist = 'Test Artist'");
            echo "<p class='info'>ℹ️ Test album removed</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Test insert failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2 class='success'>✅ Albums table fix completed!</h2>";
    
} catch (PDOException $e) {
    echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
}

echo "
    <div style='margin-top: 30px; padding: 20px; background: #f0f0f0; border-radius: 5px;'>
        <h3>🎯 Next Steps:</h3>
        <ol>
            <li><a href='admin_hub.php'>Go to Admin Hub</a></li>
            <li>Navigate to <strong>Music</strong> section</li>
            <li>Try adding an album again</li>
            <li>It should work without errors now!</li>
        </ol>
    </div>
</body>
</html>";
?>
