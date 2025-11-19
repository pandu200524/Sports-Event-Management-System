<?php
session_start();
header('Content-Type: application/json');

// 1. Enhanced security checks
if (!isset($_SESSION['admin_username'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

// 2. Validate input more thoroughly
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Valid event ID required"]);
    exit();
}

$event_id = intval($_GET['id']);
if ($event_id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid event ID"]);
    exit();
}

// 3. Database connection with error handling
try {
    $conn = new mysqli("localhost", "root", "", "sports", 3307);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    // 4. Verify table name matches your database (event vs events)
    $stmt = $conn->prepare("SELECT 
        id, name, tag, overview, highlights, 
        cover_image_url, image_url, 
        indoor_sports, outdoor_sports, racket_sports, 
        rules, about, start_date, end_date, 
        location, contact 
        FROM event WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $event_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    // 5. Handle no results found case
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Event not found"]);
        exit();
    }

    $event = $result->fetch_assoc();
    
    // 6. Process JSON fields if needed
    $json_fields = ['indoor_sports', 'outdoor_sports', 'racket_sports', 'rules'];
    foreach ($json_fields as $field) {
        if (isset($event[$field]) && !empty($event[$field])) {
            $decoded = json_decode($event[$field], true);
            $event[$field] = $decoded !== null ? $decoded : $event[$field];
        }
    }

    // 7. Ensure URLs are included in the response
    $event['cover_image_url'] = isset($event['cover_image_url']) ? $event['cover_image_url'] : '';
    $event['detail_image_url'] = isset($event['image_url']) ? $event['image_url'] : '';

    // 8. Return successful response
    echo json_encode($event);
    
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    // 9. Ensure resources are cleaned up
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>