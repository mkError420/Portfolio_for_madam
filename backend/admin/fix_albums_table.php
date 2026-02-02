<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

echo "<h2>Albums Table Structure Fix</h2>";

try {
    // Check current table structure
    echo "<h3>Current Albums Table Structure:</h3>";
    $result = $pdo->query("DESCRIBE albums");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
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
    
    // Get existing columns
    $existing_columns = array_column($columns, 'Field');
    
    // Columns to add
    $required_columns = [
        'artist' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER title",
        'release_date' => "DATE NULL AFTER artist",
        'genre' => "VARCHAR(100) DEFAULT '' AFTER release_date",
        'type' => "ENUM('album', 'single', 'ep') DEFAULT 'album' AFTER genre",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER type",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
    ];
    
    echo "<h3>Adding Missing Columns:</h3>";
    
    foreach ($required_columns as $column_name => $column_definition) {
        if (!in_array($column_name, $existing_columns)) {
            try {
                $sql = "ALTER TABLE albums ADD COLUMN $column_name $column_definition";
                $pdo->exec($sql);
                echo "<p style='color: green;'>✅ Added column: $column_name</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>❌ Error adding $column_name: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: blue;">ℹ️ Column $column_name already exists</p>";
        }
    }
    
    // Show final structure
    echo "<h3>Updated Albums Table Structure:</h3>";
    $result = $pdo->query("DESCRIBE albums");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
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
    
    // Test adding a sample album
    echo "<h3>Testing Album Insert:</h3>";
    try {
        $stmt = $pdo->prepare("INSERT INTO albums (title, artist, release_date, genre, type) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute(['Test Album', 'Test Artist', '2024-01-01', 'Pop', 'album']);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Test album added successfully!</p>";
            
            // Remove test album
            $pdo->exec("DELETE FROM albums WHERE title = 'Test Album' AND artist = 'Test Artist'");
            echo "<p style='color: blue;'>ℹ️ Test album removed</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Test insert failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>✅ Albums table fix completed!</h3>";
    echo "<p><a href='admin_hub.php'>← Back to Admin Hub</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
?>
