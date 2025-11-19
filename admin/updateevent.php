<?php
session_start();
header('Content-Type: application/json');

// Authentication Check
if (!isset($_SESSION['admin_username'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

// Request Method Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

// Required Field Check
$required_fields = ['event_id', 'event_name', 'event_tag', 'event_overview', 'event_highlights',
                    'start_date', 'end_date', 'location', 'contact', 'cover_image_url', 'image_url',
                    'indoor_sports', 'outdoor_sports', 'racket_sports'];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        http_response_code(400);
        echo json_encode(["error" => "Required field missing: $field"]);
        exit();
    }
}

// Process Input
$event_id = (int)$_POST['event_id'];
$event_name = trim($_POST['event_name']);
$event_tag = trim($_POST['event_tag']);
$event_overview = trim($_POST['event_overview']);
$event_highlights = trim($_POST['event_highlights']);
$cover_image_url = trim($_POST['cover_image_url']);
$image_url = trim($_POST['image_url']);
$location = trim($_POST['location']);
$contact = trim($_POST['contact']);
$start_date = trim($_POST['start_date']);
$end_date = trim($_POST['end_date']);

// Process JSON fields
function formatJsonField($input) {
    if (empty($input)) return '[]';
    
    // Check if already JSON
    $decoded = json_decode($input);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (!is_array($decoded)) {
            $decoded = [$decoded];
        }
        return json_encode($decoded);
    }
    
    // Process comma-separated values
    $items = array_filter(array_map('trim', explode(',', $input)));
    return json_encode($items);
}

$indoor_sports = formatJsonField($_POST['indoor_sports']);
$outdoor_sports = formatJsonField($_POST['outdoor_sports']);
$racket_sports = formatJsonField($_POST['racket_sports']);
$rules = formatJsonField($_POST['rules'] ?? '');
$about = isset($_POST['about']) ? trim($_POST['about']) : '';

try {
    // Database Connection
    $conn = new mysqli("localhost", "root", "", "sports", 3307);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Check if updated_at column exists
    $check_column = $conn->query("SHOW COLUMNS FROM event LIKE 'updated_at'");
    $has_updated_at = $check_column->num_rows > 0;
    
    // Prepare SQL statement based on column existence
    if ($has_updated_at) {
        $sql = "UPDATE event SET 
                name = ?, tag = ?, overview = ?, highlights = ?,
                cover_image_url = ?, image_url = ?, indoor_sports = ?,
                outdoor_sports = ?, racket_sports = ?, rules = ?, about = ?,
                start_date = ?, end_date = ?, location = ?, contact = ?,
                updated_at = NOW()
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssssssssssssssi", 
            $event_name, $event_tag, $event_overview, $event_highlights,
            $cover_image_url, $image_url, $indoor_sports, $outdoor_sports,
            $racket_sports, $rules, $about, $start_date, $end_date,
            $location, $contact, $event_id
        );
    } else {
        $sql = "UPDATE event SET 
                name = ?, tag = ?, overview = ?, highlights = ?,
                cover_image_url = ?, image_url = ?, indoor_sports = ?,
                outdoor_sports = ?, racket_sports = ?, rules = ?, about = ?,
                start_date = ?, end_date = ?, location = ?, contact = ?
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sssssssssssssssi", 
            $event_name, $event_tag, $event_overview, $event_highlights,
            $cover_image_url, $image_url, $indoor_sports, $outdoor_sports,
            $racket_sports, $rules, $about, $start_date, $end_date,
            $location, $contact, $event_id
        );
    }
    
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    echo json_encode(["success" => true, "message" => "Event updated successfully"]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>