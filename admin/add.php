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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_name = trim($_POST['event_name']);
    $event_tag = trim($_POST['event_tag']);
    $event_overview = trim($_POST['event_overview']);
    $event_highlights = trim($_POST['event_highlights']);
    $cover_image = trim($_POST['cover_image']); 
    $event_image = trim($_POST['event_image']);
    $location = trim($_POST['location']);
    $contact = trim($_POST['contact']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $indoor_sports = !empty($_POST['indoor_sports']) ? json_encode(array_map('trim', explode(",", $_POST['indoor_sports']))) : NULL;
    $outdoor_sports = !empty($_POST['outdoor_sports']) ? json_encode(array_map('trim', explode(",", $_POST['outdoor_sports']))) : NULL;
    $racket_sports = !empty($_POST['racket_sports']) ? json_encode(array_map('trim', explode(",", $_POST['racket_sports']))) : NULL;
    $rules = !empty($_POST['rules']) ? json_encode(array_map('trim', explode(",", $_POST['rules']))) : NULL;
    $about = !empty($_POST['about']) ? trim($_POST['about']) : NULL;

    if (empty($event_name) || empty($event_tag) || empty($event_overview) || empty($event_highlights) || empty($cover_image) || empty($event_image) || empty($start_date) || empty($end_date) || empty($location) || empty($contact)) {
        $error_message = "All fields except 'Rules' and 'About' are required!";
    } else {
        $is_payable = (strtolower($event_name) === 'udgam') ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO event (name, tag, overview, highlights, cover_image_url, image_url, indoor_sports, outdoor_sports, racket_sports, rules, about, start_date, end_date, location, contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssssss", $event_name, $event_tag, $event_overview, $event_highlights, $cover_image, $event_image, $indoor_sports, $outdoor_sports, $racket_sports, $rules, $about, $start_date, $end_date, $location, $contact);

        if ($stmt->execute()) {
            $success_message = "Event added successfully!";
            // Changed to use JavaScript alert instead of redirecting immediately
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
            text-align: center;
            padding: 20px;
            min-width: 800px; /* Prevents shrinking below this width */
        }
        form {
            width: 600px; /* Fixed width */
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        .form-group, .textarea-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .form-group label, .textarea-group label {
            width: 30%;
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
            line-height: 35px;
        }
        .form-group input, .form-group textarea, .textarea-group textarea {
            width: 70%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .textarea-group textarea {
            height: 50px;
        }
        .section-title {
            text-align: left;
            margin-left: 0;
            margin-top: 5px;
            margin-bottom: 15px;
            font-size: 18px;
            color: #ff9800;
            padding-left: 15px;
            border-left: 3px solid #007bff;
        }
        .required {
            color: red;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .note {
            font-size: 12px;
            color: #666;
        }
        .btn-back {
            background-color: red;
            margin-right: 10px;
        }
        .btn-back:hover {
            background-color: orange;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        .full-width-textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            height: 50px;
            margin-bottom: 10px;
        }
        h1 {
            margin-bottom: 15px;
        }
    </style>
    <script>
        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }
    </script>
</head>
<body>
    <h1>Add New Event</h1>

    <?php if (!empty($error_message)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    
    <?php if (isset($show_success_alert) && $show_success_alert): ?>
        <script>
            alert("Event added successfully!");
            window.location.href = 'dashboard.php';
        </script>
    <?php endif; ?>

    <form action="add.php" method="post">
        <div class="form-group">
            <label for="event_name">Event Name <span class="required">*</span></label>
            <input type="text" id="event_name" name="event_name" required>
        </div>
        
        <div class="form-group">
            <label for="event_tag">Event Tag <span class="required">*</span></label>
            <input type="text" id="event_tag" name="event_tag" required>
        </div>
        
        <div class="textarea-group">
            <label for="event_overview">Event Overview <span class="required">*</span></label>
            <textarea id="event_overview" name="event_overview" required></textarea>
        </div>
        
        <div class="textarea-group">
            <label for="event_highlights">Event Highlights <span class="required">*</span></label>
            <textarea id="event_highlights" name="event_highlights" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="cover_image">Cover Image URL <span class="required">*</span></label>
            <input type="text" id="cover_image" name="cover_image" required>
        </div>
        
        <div class="form-group">
            <label for="event_image">Event Image URL <span class="required">*</span></label>
            <input type="text" id="event_image" name="event_image" required>
        </div>

        <h3 class="section-title">Sports Conducted</h3>
        
        <div class="form-group">
            <label for="indoor_sports">Indoor Sports</label>
            <input type="text" id="indoor_sports" name="indoor_sports" placeholder="Comma separated values">
        </div>
        
        <div class="form-group">
            <label for="outdoor_sports">Outdoor Sports</label>
            <input type="text" id="outdoor_sports" name="outdoor_sports" placeholder="Comma separated values">
        </div>
        
        <div class="form-group">
            <label for="racket_sports">Racket Sports</label>
            <input type="text" id="racket_sports" name="racket_sports" placeholder="Comma separated values">
        </div>
        
        <h3 class="section-title">Rules</h3>
        <textarea class="full-width-textarea" id="rules" name="rules" placeholder="Comma separated values"></textarea>

        <h3 class="section-title">About</h3>
        <textarea class="full-width-textarea" id="about" name="about"></textarea>

        <h3 class="section-title">Details</h3>
        
        <div class="form-group">
            <label for="start_date">Start Date <span class="required">*</span></label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        
        <div class="form-group">
            <label for="end_date">End Date <span class="required">*</span></label>
            <input type="date" id="end_date" name="end_date" required>
        </div>
        
        <div class="form-group">
            <label for="location">Location <span class="required">*</span></label>
            <input type="text" id="location" name="location" required>
        </div>
        
        <div class="form-group">
            <label for="contact">Contact <span class="required">*</span></label>
            <input type="text" id="contact" name="contact" required>
        </div>

        <div class="btn-container">
            <button type="submit">Add Event</button>
            <button type="button" class="btn-back" onclick="goToDashboard()">Back</button>
        </div>
    </form>
</body>
</html>