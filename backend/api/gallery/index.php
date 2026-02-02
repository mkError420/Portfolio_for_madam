<?php
require_once '../../../config/database.php';
require_once '../../../config/cors.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'GET':
        get_gallery_data($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function get_gallery_data($db) {
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    
    $query = "
        SELECT 
            id,
            title,
            description,
            image_url,
            thumbnail_url,
            category
        FROM gallery
    ";
    
    if ($category && $category !== 'all') {
        $query .= " WHERE category = :category";
    }
    
    $query .= " ORDER BY created_at DESC";

    try {
        $stmt = $db->prepare($query);
        
        if ($category && $category !== 'all') {
            $stmt->bindParam(':category', $category);
        }
        
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process images to ensure they have proper URLs
        foreach ($images as &$image) {
            $image['src'] = $image['image_url'];
            $image['thumbnail'] = $image['thumbnail_url'] ?: $image['image_url'];
            
            // Fallback placeholders if no image URL
            if (!$image['image_url']) {
                $image['src'] = 'https://via.placeholder.com/800x600/2a2a2a/ffffff?text=Gallery+Image';
                $image['thumbnail'] = 'https://via.placeholder.com/400x300/2a2a2a/ffffff?text=Gallery+Thumbnail';
            }
        }

        http_response_code(200);
        echo json_encode($images);

    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Database error: " . $exception->getMessage()
        ));
    }
}
?>
