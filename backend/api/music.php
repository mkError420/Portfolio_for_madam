<?php
// Remove all output buffering
if (ob_get_level()) ob_end_clean();

// Set CORS headers FIRST - before any other output
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');
    header('Access-Control-Max-Age: 86400');
    header('Content-Length: 0');
    exit(0);
}

// Set content type
header('Content-Type: application/json; charset=UTF-8');

// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

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
                    'tracks' => []
                ];
            }, $albums),
            'singles' => array_values(array_filter(array_map(function($album) {
                if ($album['type'] === 'single') {
                    return [
                        'id' => (int)$album['id'],
                        'title' => $album['title'],
                        'artist' => $album['artist'],
                        'release_date' => $album['release_date'],
                        'genre' => $album['genre'],
                        'cover' => "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=" . urlencode($album['title']),
                        'duration' => "3:30"
                    ];
                }
                return null;
            }, $albums))),
            'albums_only' => array_values(array_filter(array_map(function($album) {
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
            }, $albums)))
        ]
    ];
    
    // Clean output and send JSON
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Error response with proper headers
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
