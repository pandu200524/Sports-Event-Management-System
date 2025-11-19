<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch events list from the correct table (`event`)
$sql = "SELECT id, name FROM event ORDER BY id DESC"; // Removed `start_date`
$result = $conn->query($sql);
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        h1 {
            color: #484622;
            margin-bottom: 30px;
        }
        
        form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background-color: white;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background-color: #b22222;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        
        button:hover {
            background-color: #8b0000;
        }
        
        .back-link {
            margin-top: 20px;
            color: #484622;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Delete Event</h1>
    <form action="deleteevent.php" method="post" onsubmit="return confirm('Are you sure you want to delete this event?');">
        <select name="event_id" required>
            <option value="" disabled selected>Select Event to Delete</option>
            <?php foreach ($events as $event): ?>
                <option value="<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Delete Event</button>
    </form>
    <a href="dashboard.php" class="back-link">Back to Dashboard</a>
</body>
</html>
