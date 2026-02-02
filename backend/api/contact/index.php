<?php
require_once '../../../config/database.php';
require_once '../../../config/cors.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'POST':
        submit_contact_form($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function submit_contact_form($db) {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));

    // Validate required fields
    if (
        empty($data->name) ||
        empty($data->email) ||
        empty($data->subject) ||
        empty($data->message)
    ) {
        http_response_code(400);
        echo json_encode(array(
            "message" => "Missing required fields",
            "errors" => array(
                "name" => empty($data->name) ? "Name is required" : null,
                "email" => empty($data->email) ? "Email is required" : null,
                "subject" => empty($data->subject) ? "Subject is required" : null,
                "message" => empty($data->message) ? "Message is required" : null
            )
        ));
        return;
    }

    // Validate email format
    if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(array(
            "message" => "Invalid email format",
            "errors" => array("email" => "Please enter a valid email address")
        ));
        return;
    }

    // Validate message length
    if (strlen(trim($data->message)) < 10) {
        http_response_code(400);
        echo json_encode(array(
            "message" => "Message too short",
            "errors" => array("message" => "Message must be at least 10 characters long")
        ));
        return;
    }

    // Sanitize input
    $name = htmlspecialchars(strip_tags($data->name));
    $email = htmlspecialchars(strip_tags($data->email));
    $subject = htmlspecialchars(strip_tags($data->subject));
    $message = htmlspecialchars(strip_tags($data->message));
    $message_type = isset($data->type) ? htmlspecialchars(strip_tags($data->type)) : 'general';

    // Validate message type
    $valid_types = array('general', 'booking', 'collaboration', 'press', 'fan');
    if (!in_array($message_type, $valid_types)) {
        $message_type = 'general';
    }

    $query = "
        INSERT INTO contact_messages 
        (name, email, subject, message_type, message) 
        VALUES 
        (:name, :email, :subject, :message_type, :message)
    ";

    try {
        $stmt = $db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message_type', $message_type);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array(
                "message" => "Contact form submitted successfully",
                "status" => "success"
            ));
        } else {
            http_response_code(500);
            echo json_encode(array(
                "message" => "Failed to submit contact form",
                "status" => "error"
            ));
        }

    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Database error: " . $exception->getMessage(),
            "status" => "error"
        ));
    }
}
?>
