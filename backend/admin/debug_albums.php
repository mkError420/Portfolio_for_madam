<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Albums Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
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
        <h1>🔍 Albums Debug</h1>
        <p>Debugging why albums aren't showing in frontend.</p>";

// Check database and filtering logic
echo "<h2>🗄️ Database Analysis</h2>";
try {
    require_once '../config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        // Get all albums
        $stmt = $pdo->query("SELECT * FROM albums ORDER BY created_at DESC");
        $all_albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>All Albums in Database:</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Title</th><th>Artist</th><th>Type</th><th>Release Date</th><th>Genre</th><th>Will Show as Album?</th></tr>";
        
        $albums_for_frontend = [];
        $singles_for_frontend = [];
        
        foreach ($all_albums as $album) {
            $is_album = $album['type'] === 'album';
            $is_single = $album['type'] === 'single';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($album['id']) . "</td>";
            echo "<td>" . htmlspecialchars($album['title']) . "</td>";
            echo "<td>" . htmlspecialchars($album['artist']) . "</td>";
            echo "<td>" . htmlspecialchars($album['type']) . "</td>";
            echo "<td>" . htmlspecialchars($album['release_date'] ?: 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($album['genre'] ?: 'N/A') . "</td>";
            echo "<td><strong>" . ($is_album ? "✅ YES" : "❌ NO") . "</strong></td>";
            echo "</tr>";
            
            if ($is_album) {
                $albums_for_frontend[] = $album;
            } elseif ($is_single) {
                $singles_for_frontend[] = $album;
            }
        }
        echo "</table>";
        
        echo "<h3>📊 Filtering Results:</h3>";
        echo "<p><strong>Total albums in database:</strong> " . count($all_albums) . "</p>";
        echo "<p><strong>Albums (type='album'):</strong> " . count($albums_for_frontend) . "</p>";
        echo "<p><strong>Singles (type='single'):</strong> " . count($singles_for_frontend) . "</p>";
        
        // Show what will be sent to frontend
        echo "<h3>📡 What API Sends to Frontend:</h3>";
        
        // Simulate the API logic
        $albums_only = array_values(array_filter(array_map(function($album) {
            if ($album['type'] === 'album') {
                return [
                    'id' => (int)$album['id'],
                    'title' => $album['title'],
                    'artist' => $album['artist'],
                    'release_date' => $album['release_date'],
                    'genre' => $album['genre'],
                    'year' => date('Y', strtotime($album['release_date'] ?: $album['created_at'])),
                    'cover' => "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=" . urlencode($album['title']),
                    'tracks' => [
                        [
                            'id' => (int)$album['id'] * 1000 + 1,
                            'title' => $album['title'] . " - Track 1",
                            'duration' => "3:45",
                            'artist' => $album['artist']
                        ],
                        [
                            'id' => (int)$album['id'] * 1000 + 2,
                            'title' => $album['title'] . " - Track 2", 
                            'duration' => "4:12",
                            'artist' => $album['artist']
                        ]
                    ]
                ];
            }
            return null;
        }, $all_albums)));
        
        echo "<p><strong>Albums array for frontend:</strong> " . count($albums_only) . "</p>";
        
        if (count($albums_only) > 0) {
            echo "<h4>Albums Data:</h4>";
            echo "<pre>" . htmlspecialchars(json_encode($albums_only, JSON_PRETTY_PRINT)) . "</pre>";
        } else {
            echo "<p class='error'>❌ No albums data for frontend!</p>";
            
            echo "<h4>🔧 Possible Issues:</h4>";
            echo "<ul>";
            echo "<li><strong>Wrong type values:</strong> Albums might not have type='album'</li>";
            echo "<li><strong>Case sensitivity:</strong> Check if type values have correct case</li>";
            echo "<li><strong>Null values:</strong> Type field might be NULL or empty</li>";
            echo "</ul>";
            
            // Show type distribution
            echo "<h4>📈 Type Distribution:</h4>";
            $type_counts = [];
            foreach ($all_albums as $album) {
                $type = $album['type'] ?: 'NULL';
                $type_counts[$type] = ($type_counts[$type] ?? 0) + 1;
            }
            
            foreach ($type_counts as $type => $count) {
                echo "<p><strong>Type '$type':</strong> $count albums</p>";
            }
        }
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "
        <div style='margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;'>
            <h3>🔧 Quick Fix:</h3>
            <p>If albums have wrong type values, you can update them:</p>
            <pre>
UPDATE albums SET type = 'album' WHERE type IS NULL OR type = '';
UPDATE albums SET type = 'single' WHERE id IN (5,6,7,8,9); -- Update singles
            </pre>
        </div>
        
        <div style='margin-top: 20px; text-align: center;'>
            <a href='admin_hub.php' class='btn'>🎛️ Admin Hub</a>
            <a href='../api/music.php' target='_blank' class='btn' style='background: #28a745;'>📡 Test API</a>
        </div>
    </div>
</body>
</html>";
?>
