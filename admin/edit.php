<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Authentication Check
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;

try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Fetch all events
    $sql = "SELECT id, name FROM event ORDER BY name ASC";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching events: " . $conn->error);
    }
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        
        /* Container to maintain form width */
        .container {
            display: flex;
            justify-content: center;
            width: 100%;
            min-height: 100vh;
        }
        
        /* Form Styles */
        form {
            width: 600px; /* Fixed width */
            min-width: 600px; /* Ensures minimum width */
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        
        .form-group input, .form-group textarea, .form-group select, .textarea-group textarea {
            width: 70%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .textarea-group textarea {
            height: 50px;
            resize: vertical;
        }
        
        /* Section Styling */
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .section-title {
            text-align: left;
            margin-top: 20px;
            margin-bottom: 15px;
            font-size: 18px;
            color: #ff9800;
            padding-left: 15px;
            border-left: 3px solid #007bff;
        }
        
        .required {
            color: red;
            margin-left: 3px;
        }
        
        /* Full Width Textarea */
        .full-width-textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            height: 50px;
            margin-bottom: 10px;
            resize: vertical;
        }
        
        /* Button Styles */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .btn-back {
            background-color: #dc3545;
            margin-right: 10px;
        }
        
        .btn-back:hover {
            background-color: #c82333;
        }
        
        /* Message Styles */
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
        }
        
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #d4edda;
            border-radius: 5px;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            text-align: center;
            position: relative;
        }
        
        .close-modal {
            position: absolute;
            right: 10px;
            top: 5px;
            font-size: 20px;
            cursor: pointer;
        }
        
        .modal-buttons {
            margin-top: 20px;
        }
        
        /* Media queries to ensure fixed form width */
        @media screen and (max-width: 650px) {
            form {
                width: 600px;
                min-width: 600px;
                overflow-x: auto;
                margin: 0;
                padding: 20px;
            }
            
            .container {
                justify-content: flex-start;
                overflow-x: auto;
                padding: 0;
            }
            
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Edit Event</h1>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3>Success!</h3>
            <p>Event updated successfully!</p>
            <div class="modal-buttons">
                <button onclick="window.location.href='dashboard.php'">Go to Dashboard</button>
            </div>
        </div>
    </div>

    <div class="container">
        <form action="updateevent.php" method="post" id="eventForm">
            <div class="form-group">
                <label for="event_select">Select Event<span class="required">*</span></label>
                <select id="event_select" name="event_id" required onchange="loadEventDetails(this.value)">
                    <option value="" disabled selected>Select an Event</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="event_name">Event Name<span class="required">*</span></label>
                <input type="text" id="event_name" name="event_name" required>
            </div>
            
            <div class="form-group">
                <label for="event_tag">Event Tag<span class="required">*</span></label>
                <input type="text" id="event_tag" name="event_tag" required>
            </div>
            
            <div class="textarea-group">
                <label for="event_overview">Event Overview<span class="required">*</span></label>
                <textarea id="event_overview" name="event_overview" required></textarea>
            </div>
            
            <div class="textarea-group">
                <label for="event_highlights">Event Highlights<span class="required">*</span></label>
                <textarea id="event_highlights" name="event_highlights" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="cover_image_url">Cover Image URL<span class="required">*</span></label>
                <input type="url" id="cover_image_url" name="cover_image_url" required>
            </div>
            
            <div class="form-group">
                <label for="image_url">Detail Image URL<span class="required">*</span></label>
                <input type="url" id="image_url" name="image_url" required>
            </div>

            <h3 class="section-title">Sports Conducted</h3>
            
            <div class="form-group">
                <label for="indoor_sports">Indoor Sports<span class="required">*</span></label>
                <input type="text" id="indoor_sports" name="indoor_sports" placeholder="Comma separated values" required>
            </div>
            
            <div class="form-group">
                <label for="outdoor_sports">Outdoor Sports<span class="required">*</span></label>
                <input type="text" id="outdoor_sports" name="outdoor_sports" placeholder="Comma separated values" required>
            </div>
            
            <div class="form-group">
                <label for="racket_sports">Racket Sports<span class="required">*</span></label>
                <input type="text" id="racket_sports" name="racket_sports" placeholder="Comma separated values" required>
            </div>
            
            <h3 class="section-title">Rules</h3>
            <textarea class="full-width-textarea" id="rules" name="rules" placeholder="Comma separated values"></textarea>

            <h3 class="section-title">About</h3>
            <textarea class="full-width-textarea" id="about" name="about"></textarea>

            <h3 class="section-title">Details</h3>
            
            <div class="form-group">
                <label for="start_date">Start Date<span class="required">*</span></label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            
            <div class="form-group">
                <label for="end_date">End Date<span class="required">*</span></label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location<span class="required">*</span></label>
                <input type="text" id="location" name="location" required>
            </div>
            
            <div class="form-group">
                <label for="contact">Contact<span class="required">*</span></label>
                <input type="text" id="contact" name="contact" required>
            </div>

            <div class="btn-container">
                <button type="submit">Update Event</button>
                <button type="button" class="btn-back" onclick="window.location.href='dashboard.php'">Back</button>
            </div>
        </form>
    </div>

    <script>
        function loadEventDetails(eventId) {
            if (!eventId) return;
            
            fetch('get_event.php?id=' + eventId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert("Error: " + data.error);
                    } else {
                        // Update form fields with event data
                        document.getElementById('event_name').value = data.name || '';
                        document.getElementById('event_tag').value = data.tag || '';
                        document.getElementById('event_overview').value = data.overview || '';
                        document.getElementById('event_highlights').value = data.highlights || '';
                        document.getElementById('cover_image_url').value = data.cover_image_url || '';
                        document.getElementById('image_url').value = data.image_url || data.detail_image_url || '';
                        
                        // Handle JSON arrays for sports
                        const formatArrayData = (fieldData) => {
                            if (!fieldData) return '';
                            try {
                                const parsed = JSON.parse(fieldData);
                                if (Array.isArray(parsed)) {
                                    return parsed.join(", ");
                                }
                                return fieldData;
                            } catch (e) {
                                return fieldData;
                            }
                        };
                        
                        document.getElementById('indoor_sports').value = formatArrayData(data.indoor_sports);
                        document.getElementById('outdoor_sports').value = formatArrayData(data.outdoor_sports);
                        document.getElementById('racket_sports').value = formatArrayData(data.racket_sports);
                        document.getElementById('rules').value = formatArrayData(data.rules);
                        document.getElementById('about').value = data.about || '';
                        
                        // Format dates properly if they exist
                        if (data.start_date) {
                            document.getElementById('start_date').value = data.start_date.split(' ')[0];
                        }
                        if (data.end_date) {
                            document.getElementById('end_date').value = data.end_date.split(' ')[0];
                        }
                        
                        document.getElementById('location').value = data.location || '';
                        document.getElementById('contact').value = data.contact || '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Failed to load event details. Please try again.");
                });
        }
        
        function showModal() {
            document.getElementById('successModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }
        
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validation
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            
            if (startDate > endDate) {
                alert("End date must be after start date");
                return false;
            }
            
            const coverImageUrl = document.getElementById('cover_image_url').value;
            const imageUrl = document.getElementById('image_url').value;
            
            if ((coverImageUrl && !/^https?:\/\//i.test(coverImageUrl)) || 
                (imageUrl && !/^https?:\/\//i.test(imageUrl))) {
                alert("Please enter a valid URL starting with http:// or https://");
                return false;
            }
            
            // Submit the form via AJAX
            const formData = new FormData(this);
            
            fetch('updateevent.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.error);
                } else {
                    showModal(); // Show success modal
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Failed to update event. Please try again.");
            });
        });
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('successModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>