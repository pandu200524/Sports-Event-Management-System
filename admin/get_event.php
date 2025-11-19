<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "sports";
$port = 3307;

$conn = new mysqli($servername, $db_username, $db_password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$event_id = $_GET['id'];
$sql = "SELECT * FROM event WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data ? $data : ["error" => "Event not found"]);

$stmt->close();
$conn->close();
?>
