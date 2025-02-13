<?php
// Database connection setup
$host = "localhost";
$dbname = "srm_sports";
$username = "root";
$password = "Rev2005@!!"; 
$port = 4307;

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'] ?? '';
    $reg_number = $_POST['reg_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $department = $_POST['department'] ?? '';
    $section = $_POST['section'] ?? '';
    $event = $_POST['event'] ?? '';
    $event_category = $_POST['event_category'] ?? '';  // Check if this column exists in DB
    $game_category = $_POST['game_category'] ?? '';
    $game = $_POST['game'] ?? '';

    // Check for empty fields
    if (empty($name)) {
        die("Error: Name is required!");
    } elseif (empty($reg_number)) {
        die("Error: Registration number is required!");
    } elseif (empty($email)) {
        die("Error: Email is required!");
    } elseif (empty($phone)) {
        die("Error: Phone number is required!");
    } elseif (empty($department)) {
        die("Error: Department is required!");
    } elseif (empty($section)) {
        die("Error: Section is required!");
    } elseif (empty($event)) {
        die("Error: Event is required!");
    } elseif (empty($game_category)) {
        die("Error: Game category is required!");
    } elseif (empty($game)) {
        die("Error: Game is required!");
    }

    // Ensure the column exists before inserting
    $stmt = $conn->prepare("INSERT INTO singleplayer (name, reg_number, email, phone, department, section, event, event_category, game_category, game) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        die("Error in SQL statement: " . $conn->error);
    }

    $stmt->bind_param("ssssssssss", $name, $reg_number, $email, $phone, $department, $section, $event, $event_category, $game_category, $game);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
