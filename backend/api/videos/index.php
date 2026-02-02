<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/cors.php';

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

        foreach ($videos as &$video) {
            $video['thumbnail'] = !empty($video['thumbnail']) 
                ? get_full_image_url($video['thumbnail']) 
                : 'https://via.placeholder.com/640x360/2a2a2a/ffffff?text=Video';
            
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
