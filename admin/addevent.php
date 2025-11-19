<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "sports";
$port = 3307;

$conn = new mysqli($servername, $db_username, $db_password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";
$success_message = "";
$show_success_alert = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $end_date = $_POST['end_date'];
    $venue = trim($_POST['venue']);
    $sport_type = trim($_POST['sport_type']);
    $participants = trim($_POST['participants']);
    $registration_deadline = $_POST['registration_deadline'];
    $organizer_name = trim($_POST['organizer_name']);
    $contact_info = trim($_POST['contact_info']);
    $event_status = $_POST['event_status'];
    $description = trim($_POST['description']);
    $cover_image_url = trim($_POST['cover_image_url']);
    $detail_image_url = trim($_POST['detail_image_url']);

    if (empty($event_name) || empty($event_date) || empty($event_time) || empty($end_date) || empty($venue) || empty($sport_type) || empty($participants) || empty($registration_deadline) || empty($organizer_name) || empty($contact_info) || empty($event_status) || empty($description) || empty($cover_image_url) || empty($detail_image_url)) {
        $error_message = "All fields are required!";
    } else {
        $is_payable = (strtolower($event_name) === 'udgam') ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_time, end_date, venue, sport_type, participants, registration_deadline, organizer_name, contact_info, event_status, description, cover_image_url, detail_image_url, is_payable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssssi", $event_name, $event_date, $event_time, $end_date, $venue, $sport_type, $participants, $registration_deadline, $organizer_name, $contact_info, $event_status, $description, $cover_image_url, $detail_image_url, $is_payable);

        if ($stmt->execute()) {
            $success_message = "Event added successfully!";
            $show_success_alert = true;
        } else {
            $error_message = "Error adding event: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: auto;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            min-width: 850px; /* Prevents shrinking below this width */
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-group label {
            width: 35%;
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 65%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .textarea-group {
            display: flex;
            margin-bottom: 15px;
        }
        .textarea-group label {
            width: 35%;
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }
        .textarea-group textarea {
            width: 65%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 100px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
        .required {
            color: red;
            margin-left: 2px;
        }
        .section-title {
            text-align: left;
            margin-left: 35%;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .section-divider {
            margin: 20px 0;
            border-top: 1px solid #eee;
        }
    </style>
    <script>
        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Add New Event</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($show_success_alert): ?>
            <script>
                alert("Event added successfully!");
                window.location.href = 'dashboard.php';
            </script>
        <?php endif; ?>
        
        <form action="addevent.php" method="post">
            <div class="form-group">
                <label for="event_name">Event Name<span class="required">*</span></label>
                <input type="text" id="event_name" name="event_name" required>
            </div>
            <div class="form-group">
                <label for="sport_type">Sport Type<span class="required">*</span></label>
                <input type="text" id="sport_type" name="sport_type" required>
            </div>
            <div class="section-divider"></div>
            <div class="section-title">Event Schedule</div>
            <div class="form-group">
                <label for="event_date">Start Date<span class="required">*</span></label>
                <input type="date" id="event_date" name="event_date" required>
            </div>
            <div class="form-group">
                <label for="event_time">Start Time<span class="required">*</span></label>
                <input type="time" id="event_time" name="event_time" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date<span class="required">*</span></label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            <div class="form-group">
                <label for="venue">Venue<span class="required">*</span></label>
                <input type="text" id="venue" name="venue" required>
            </div>
            <div class="section-divider"></div>
            <div class="section-title">Participation Details</div>
            <div class="form-group">
                <label for="participants">Participants<span class="required">*</span></label>
                <input type="text" id="participants" name="participants" required>
            </div>
            <div class="form-group">
                <label for="registration_deadline">Registration Deadline<span class="required">*</span></label>
                <input type="date" id="registration_deadline" name="registration_deadline" required>
            </div>
            <div class="section-divider"></div>
            <div class="section-title">Contact Information</div>
            <div class="form-group">
                <label for="organizer_name">Organizer Name<span class="required">*</span></label>
                <input type="text" id="organizer_name" name="organizer_name" required>
            </div>
            <div class="form-group">
                <label for="contact_info">Contact Information<span class="required">*</span></label>
                <input type="text" id="contact_info" name="contact_info" required>
            </div>
            <div class="section-divider"></div>
            <div class="section-title">Event Status & Description</div>
            <div class="form-group">
                <label for="event_status">Event Status<span class="required">*</span></label>
                <select id="event_status" name="event_status" required>
                    <option value="">Select Status</option>
                    <option value="Upcoming">Upcoming</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="textarea-group">
                <label for="description">Description<span class="required">*</span></label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="section-divider"></div>
            <div class="section-title">Event Images</div>
            <div class="form-group">
                <label for="cover_image_url">Cover Image URL<span class="required">*</span></label>
                <input type="text" id="cover_image_url" name="cover_image_url" required>
            </div>
            <div class="form-group">
                <label for="detail_image_url">Detail Image URL<span class="required">*</span></label>
                <input type="text" id="detail_image_url" name="detail_image_url" required>
            </div>
            <div class="btn-container">
                <button type="button" class="btn btn-secondary" onclick="goToDashboard()">Back</button>
                <button type="submit" class="btn btn-primary">Add Event</button>
            </div>
        </form>
    </div>
</body>
</html>