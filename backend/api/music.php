<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

try {
    // Get all albums and singles
    $stmt = $pdo->query("SELECT * FROM albums ORDER BY created_at DESC");
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $response = [
        'success' => true,
        'data' => [
            'albums' => array_map(function($album) {
                return [
                    'id' => (int)$album['id'],
                    'title' => $album['title'],
                    'artist' => $album['artist'],
                    'release_date' => $album['release_date'],
                    'genre' => $album['genre'],
                    'type' => $album['type'],
                    'created_at' => $album['created_at'],
                    'updated_at' => $album['updated_at'],
                    'cover' => "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=" . urlencode($album['title']),
                    'tracks' => [] // You can expand this later
                ];
            }, $albums),
            'singles' => array_filter(array_map(function($album) {
                if ($album['type'] === 'single') {
                    return [
                        'id' => (int)$album['id'],
                        'title' => $album['title'],
                        'artist' => $album['artist'],
                        'release_date' => $album['release_date'],
                        'genre' => $album['genre'],
                        'cover' => "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=" . urlencode($album['title']),
                        'duration' => "3:30" // Default duration
                    ];
                }
                return null;
            }, $albums)),
            'albums_only' => array_filter(array_map(function($album) {
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
            }, $albums))
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
