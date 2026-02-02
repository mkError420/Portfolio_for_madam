<?php
require_once '../../../config/database.php';
require_once '../../../config/cors.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'GET':
        get_music_data($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function get_music_data($db) {
    $query = "
        SELECT 
            a.id as album_id,
            a.title as album_title,
            a.year,
            a.cover_image,
            a.description,
            t.id as track_id,
            t.title as track_title,
            t.duration,
            t.artist,
            t.track_number
        FROM albums a
        LEFT JOIN tracks t ON a.id = t.album_id
        ORDER BY a.year DESC, t.track_number ASC
    ";

    try {
        $stmt = $db->prepare($query);
        $stmt->execute();

        $albums = array();
        $current_album = null;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($current_album === null || $current_album['id'] != $row['album_id']) {
                if ($current_album !== null) {
                    $albums[] = $current_album;
                }
                
                $current_album = array(
                    'id' => $row['album_id'],
                    'title' => $row['album_title'],
                    'year' => $row['year'],
                    'cover' => $row['cover_image'],
                    'description' => $row['description'],
                    'tracks' => array()
                );
            }

            if ($row['track_id']) {
                $current_album['tracks'][] = array(
                    'id' => $row['track_id'],
                    'title' => $row['track_title'],
                    'duration' => $row['duration'],
                    'artist' => $row['artist']
                );
            }
        }

        if ($current_album !== null) {
            $albums[] = $current_album;
        }

        // Get singles
        $singles_query = "
            SELECT 
                id,
                title,
                duration,
                artist,
                cover_image as cover,
                DATE_FORMAT(release_date, '%Y') as release_date
            FROM singles
            ORDER BY release_date DESC
        ";

        $singles_stmt = $db->prepare($singles_query);
        $singles_stmt->execute();
        $singles = $singles_stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = array(
            'albums' => $albums,
            'singles' => $singles
        );

        http_response_code(200);
        echo json_encode($response);

    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Database error: " . $exception->getMessage()
        ));
    }
}
?>
