<?php
require_once '../../../config/database.php';
require_once '../../../config/cors.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'GET':
        get_videos_data($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function get_videos_data($db) {
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    
    $query = "
        SELECT 
            id,
            title,
            description,
            video_id,
            thumbnail,
            duration,
            category,
            views,
            DATE_FORMAT(release_date, '%Y') as release_date,
            venue
        FROM videos
    ";
    
    if ($category) {
        $query .= " WHERE category = :category";
    }
    
    $query .= " ORDER BY release_date DESC, created_at DESC";

    try {
        $stmt = $db->prepare($query);
        
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        
        $stmt->execute();
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group videos by category
        $grouped_videos = array(
            'music_videos' => array(),
            'live_performances' => array(),
            'behind_scenes' => array()
        );

        foreach ($videos as $video) {
            $video['thumbnail'] = $video['thumbnail'] ?: 'https://via.placeholder.com/640x360/2a2a2a/ffffff?text=Video';
            
            switch($video['category']) {
                case 'music_video':
                    $grouped_videos['music_videos'][] = $video;
                    break;
                case 'live_performance':
                    $grouped_videos['live_performances'][] = $video;
                    break;
                case 'behind_scenes':
                    $grouped_videos['behind_scenes'][] = $video;
                    break;
            }
        }

        if ($category) {
            // Return specific category
            http_response_code(200);
            echo json_encode($videos);
        } else {
            // Return all grouped videos
            http_response_code(200);
            echo json_encode($grouped_videos);
        }

    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Database error: " . $exception->getMessage()
        ));
    }
}
?>
