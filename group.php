<?php
// Start session
session_start();

// Enable error reporting (Remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "Rev2005@!!";
$dbname = "srm_sports";
$port = 4307;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Required fields
    $required_fields = ['name', 'reg_number', 'email', 'phone', 'department', 'section', 'event', 'game_category', 'game'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die("Error: '$field' is required!");
        }
    }

    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $reg_number = trim($_POST['reg_number']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $department = trim($_POST['department']);
    $section = trim($_POST['section']);
    $event = trim($_POST['event']);
    $event_category = isset($_POST['event_category']) ? trim($_POST['event_category']) : null;
    $game_category = trim($_POST['game_category']);
    $game = trim($_POST['game']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format!");
    }

    // Validate phone number (basic validation)
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        die("Error: Invalid phone number!");
    }

    // Insert main player data
    $sql = "INSERT INTO groupplayers (name, reg_number, email, phone, department, section, event, event_category, game_category, game) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("ssssssssss", $name, $reg_number, $email, $phone, $department, $section, $event, $event_category, $game_category, $game);

    if (!$stmt->execute()) {
        die("Error: " . $stmt->error);
    }

    $group_id = $stmt->insert_id; // Get the last inserted ID
    $stmt->close();

    // Insert team members (if any)
    if (isset($_POST['team_member_name']) && isset($_POST['team_member_reg_number'])) {
        $team_member_names = $_POST['team_member_name'];
        $team_member_reg_numbers = $_POST['team_member_reg_number'];

        if (count($team_member_names) !== count($team_member_reg_numbers)) {
            die("Error: Team member data mismatch!");
        }

        $sql_team = "INSERT INTO team_members (group_id, name, reg_number) VALUES (?, ?, ?)";
        $stmt_team = $conn->prepare($sql_team);

        if (!$stmt_team) {
            die("SQL error: " . $conn->error);
        }

        foreach ($team_member_names as $index => $team_member_name) {
            $team_member_reg_number = $team_member_reg_numbers[$index];

            // Sanitize input
            $team_member_name = trim($team_member_name);
            $team_member_reg_number = trim($team_member_reg_number);

            $stmt_team->bind_param("iss", $group_id, $team_member_name, $team_member_reg_number);

            if (!$stmt_team->execute()) {
                die("Error: " . $stmt_team->error);
            }
        }

        $stmt_team->close();
    }

    echo "Registration successful!";
}

// Close database connection
$conn->close();
?>
