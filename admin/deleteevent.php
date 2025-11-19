<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If admin is not logged in, redirect to login page
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

// Database connection details
$host = "localhost";
$username = "root";
$password = "";
$database = "sports";
$port = 3307;

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (!isset($_POST['event_id']) || empty($_POST['event_id'])) {
        echo "<script>
                alert('Invalid event selection!');
                window.location.href = 'delete.php';
              </script>";
        exit();
    }

    // Get event ID from form
    $event_id = intval($_POST['event_id']); // Ensure integer ID for security

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM event WHERE id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Event deleted successfully!');
                window.location.href = 'dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting event: " . $stmt->error . "');
                window.location.href = 'delete.php';
              </script>";
    }

    $stmt->close();
} else {
    // Redirect if accessed directly
    header("Location: delete.php");
    exit();
}

$conn->close();
?>
