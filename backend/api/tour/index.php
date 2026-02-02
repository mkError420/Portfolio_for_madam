<?php
require_once '../../../config/database.php';
require_once '../../../config/cors.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'GET':
        get_tour_data($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function get_tour_data($db) {
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    $query = "
        SELECT 
            id,
            date,
            venue,
            city,
            country,
            status,
            ticket_url,
            price_range as price,
            special_notes as special
        FROM tour_dates
    ";
    
    if ($status && $status !== 'all') {
        $query .= " WHERE status = :status";
    }
    
    $query .= " ORDER BY date ASC";

    try {
        $stmt = $db->prepare($query);
        
        if ($status && $status !== 'all') {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        $tour_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format dates and add additional info
        foreach ($tour_dates as &$date) {
            $date_obj = new DateTime($date['date']);
            $date['formatted_date'] = $date_obj->format('Y-m-d');
            $date['display_date'] = $date_obj->format('M j, Y');
            
            // Add ticket URL if not present
            if (!$date['ticket_url'] && $date['status'] === 'upcoming') {
                $date['ticket_url'] = '#'; // Default placeholder
            }
        }

        http_response_code(200);
        echo json_encode($tour_dates);

    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Database error: " . $exception->getMessage()
        ));
    }
}
?>
