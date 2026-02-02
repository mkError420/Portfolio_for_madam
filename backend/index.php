<?php
// API Router
require_once 'config/cors.php';

header("Content-Type: application/json; charset=UTF-8");

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI
$request_uri = strtok($request_uri, '?');

// Route the request
switch ($request_uri) {
    case '/api/music':
    case '/api/music/':
        require_once 'api/music/index.php';
        break;
    case '/api/videos':
    case '/api/videos/':
        require_once 'api/videos/index.php';
        break;
    case '/api/gallery':
    case '/api/gallery/':
        require_once 'api/gallery/index.php';
        break;
    case '/api/tour':
    case '/api/tour/':
        require_once 'api/tour/index.php';
        break;
    case '/api/contact':
    case '/api/contact/':
        require_once 'api/contact/index.php';
        break;
    default:
        // API documentation
        http_response_code(200);
        echo json_encode(array(
            "name" => "Singer Portfolio API",
            "version" => "1.0.0",
            "endpoints" => array(
                "GET /api/music" => "Get all albums and singles",
                "GET /api/videos" => "Get all videos grouped by category",
                "GET /api/videos?category=music_video" => "Get videos by category",
                "GET /api/gallery" => "Get all gallery images",
                "GET /api/gallery?category=performance" => "Get images by category",
                "GET /api/tour" => "Get all tour dates",
                "GET /api/tour?status=upcoming" => "Get tour dates by status",
                "POST /api/contact" => "Submit contact form"
            )
        ));
        break;
}
?>
