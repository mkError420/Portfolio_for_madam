<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/cors.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'GET') {
    get_music_data($db);
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed"));
}

function get_music_data($db) {
    try {
        // 1. Fetch Albums
        $query = "SELECT * FROM albums ORDER BY release_year DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Fetch Tracks for each Album
        foreach ($albums as &$album) {
            // Process cover image URL
            $album['cover_image'] = !empty($album['cover_image']) 
                ? get_full_image_url($album['cover_image']) 
                : 'https://via.placeholder.com/300x300?text=Album';
            
            // Fetch tracks for this album
            $track_query = "SELECT * FROM tracks WHERE album_id = :album_id ORDER BY track_number ASC";
            $track_stmt = $db->prepare($track_query);
            $track_stmt->bindParam(':album_id', $album['id']);
            $track_stmt->execute();
            $album['tracks'] = $track_stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 3. Fetch Singles
        $singles_query = "SELECT * FROM singles ORDER BY release_date DESC";
        $singles_stmt = $db->prepare($singles_query);
        $singles_stmt->execute();
        $singles = $singles_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure single covers have valid placeholder if empty
        foreach ($singles as &$single) {
            $single['cover_image'] = !empty($single['cover_image']) 
                ? get_full_image_url($single['cover_image']) 
                : 'https://via.placeholder.com/300x300?text=Single';
        }

        // Return combined data structure
        http_response_code(200);
        echo json_encode(array(
            "albums" => $albums,
            "singles" => $singles
        ));

    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Database error: " . $exception->getMessage()
        ));
    }
}

function get_full_image_url($path) {
    if (empty($path)) return null;
    if (strpos($path, 'http') === 0) return $path;
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $api_pos = strpos($script_name, '/api/');
    
    // Determine backend root based on script location
    $root = ($api_pos !== false) ? substr($script_name, 0, $api_pos) : dirname($script_name);
    $root = rtrim($root, '/\\');
    
    return $protocol . "://" . $host . $root . '/admin/' . ltrim($path, '/');
}
?>