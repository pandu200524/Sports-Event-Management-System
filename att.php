<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registration number from session or database
if (isset($_SESSION['reg_number'])) {
    $reg_number = $_SESSION['reg_number'];
} else {
    $reg_number = ""; // Default value if not set
}

// Function to get user details
function getUserDetails($conn, $app_number) {
    $query = "SELECT name, email, phone, department, section FROM singleplayer WHERE reg_number = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    $query = "SELECT name, email, phone, department, section FROM groupplayers WHERE reg_number = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    $query = "SELECT name, email FROM volunteers WHERE reg_number = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return [];
}

$userDetails = getUserDetails($conn, $reg_number);

$success_message = $error_message = '';

// Process attendance verification
if (isset($_POST['verify_attendance'])) {
    $event_id = $_POST['event_id'];
    $reg_number = $_POST['reg_number'];
    $verification_code = $_POST['verification_code'];
    
    // Verify the code is active and valid
    $code_query = "SELECT id FROM attendance_verification 
                  WHERE event_id = ? AND verification_code = ? AND is_active = 1";
    $stmt = $conn->prepare($code_query);
    $stmt->bind_param("is", $event_id, $verification_code);
    $stmt->execute();
    $code_result = $stmt->get_result();
    
    if ($code_result->num_rows > 0) {
        // Check if registration number exists in any of the tables
        $found = false;
        $person_id = null;
        $person_type = null;
        
        // Check in singleplayer table
        $query = "SELECT id FROM singleplayer WHERE reg_number = ? AND event = (SELECT name FROM event WHERE id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $reg_number, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $person_id = $result->fetch_assoc()['id'];
            $person_type = 'singleplayer';
            $found = true;
        } else {
            // Check in groupplayers table
            $query = "SELECT id FROM groupplayers WHERE reg_number = ? AND event = (SELECT name FROM event WHERE id = ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $reg_number, $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $person_id = $result->fetch_assoc()['id'];
                $person_type = 'groupplayer';
                $found = true;
            } else {
                // Check in volunteers table
                $query = "SELECT volunteer_id FROM volunteers WHERE reg_number = ? AND event_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", $reg_number, $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $person_id = $result->fetch_assoc()['volunteer_id'];
                    $person_type = 'volunteer';
                    $found = true;
                }
            }
        }
        
        if ($found) {
            // Check if attendance already marked
            $check_query = "SELECT id FROM attendance_records 
                          WHERE person_id = ? AND person_type = ? AND event_id = ? AND verification_code = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("isis", $person_id, $person_type, $event_id, $verification_code);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $success_message = "Your attendance has already been marked for this event.";
            } else {
                // Insert new attendance record
                $insert_query = "INSERT INTO attendance_records (person_id, reg_number, person_type, event_id, verification_code) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("issis", $person_id, $reg_number, $person_type, $event_id, $verification_code);
                
                if ($stmt->execute()) {
                    $success_message = "Attendance marked successfully!";
                } else {
                    $error_message = "Error marking attendance: " . $conn->error;
                }
            }
        } else {
            $error_message = "Registration number not found for this event.";
        }
    } else {
        $error_message = "Invalid or expired verification code.";
    }
}

// Get all events for dropdown
$events_query = "SELECT id, name FROM event ORDER BY name";
$events_result = $conn->query($events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Attendance Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header, footer {
            background-color: #484622;
            color: white;
            padding: 15px 0;
            position: relative;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-size: 24px;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        .user-profile {
            position: relative;
            display: inline-block;
        }
        .profile-icon {
            font-size: 24px;
            color: white;
            cursor: pointer;
            width: 40px;
            height: 40px;
            background-color: #5a582d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        .profile-icon:hover {
            background-color: #706e3c;
        }
        .dropdown-menu {
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            z-index: 100;
            display: none;
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-menu a, .dropdown-menu button {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
            border: none;
            background: none;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        .dropdown-menu a:hover, .dropdown-menu button:hover {
            background-color: #f5f5f5;
        }
        .dropdown-menu .user-info {
            padding: 16px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
        }
        .dropdown-menu .user-info .user-name {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .dropdown-menu .user-info .user-email {
            font-size: 14px;
            color: #777;
            margin-top: 5px;
        }
        .logout-btn {
            color: #d9534f !important;
            border-bottom: none !important;
        }
        footer {
            background-color: #484622;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }
        .social-media {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .social-media a {
            color: #fff;
            font-size: 1.5rem;
            transition: color 0.3s;
        }
        .social-media a:hover {
            color: #d1a74a;
        }
        .form-heading {
            background-color: #484622;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
            <h1 class="navbar-brand"><strong>Sports Portal</strong></h1>
            <div class="user-profile">
                <div class="profile-icon" id="profileIcon"><i class="fas fa-user"></i></div>
                <div class="dropdown-menu" id="profileDropdown">
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($reg_number); ?></div>
                        <?php if (isset($userDetails['email'])): ?>
                            <div class="user-email"><?php echo htmlspecialchars($userDetails['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    <a href="my-registrations.php"><i class="fas fa-clipboard-list"></i> My Registrations</a>
                    <a href="volunteers.php"><i class="fas fa-hands-helping"></i> Volunteers</a>
                    <a href="tournaments.php"><i class="fas fa-trophy"></i> Tournaments</a>
                    <a href="javascript:void(0);" onclick="showSettingsForm()"><i class="fas fa-cog"></i> Settings</a>
                    <button type="button" class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="form-heading">Event Attendance Verification</div>
        <div class="card">
            <div class="card-body">
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Select Event:</label>
                        <select class="form-select" name="event_id" id="event_id" required>
                            <option value="">-- Select Event --</option>
                            <?php while ($event = $events_result->fetch_assoc()): ?>
                                <option value="<?php echo $event['id']; ?>" <?php echo (isset($_POST['event_id']) && $_POST['event_id'] == $event['id']) ? 'selected' : ''; ?>>
                                    <?php echo $event['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reg_number" class="form-label">Registration Number:</label>
                        <input type="text" class="form-control" name="reg_number" id="reg_number" 
                               value="<?= htmlspecialchars($reg_number) ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="verification_code" class="form-label">Verification Code:</label>
                        <input type="text" class="form-control" name="verification_code" id="verification_code" required
                               placeholder="Enter the code provided by your event coordinator">
                    </div>
                    
                    <button type="submit" name="verify_attendance" class="btn text-white mt-3" style="background-color: #484622;">Verify Attendance</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Back to Sports Portal Button -->
    <div class="text-center mt-4">
        <a href="open_single_group.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Sports Portal
        </a>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 SRM University AP - All Rights Reserved</p>
        <div class="social-media">
            <a href="https://www.facebook.com/SRMUAP/" target="_blank">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="https://www.linkedin.com/school/srmuap/" target="_blank">
                <i class="fab fa-linkedin"></i>
            </a>
            <a href="https://www.instagram.com/sportscouncilsrmuap/" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://x.com/SRMUAP" target="_blank">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://www.youtube.com/c/SRMUniversityAP" target="_blank">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </footer>

    <script>
        document.getElementById('profileIcon').addEventListener('click', function() {
            document.getElementById('profileDropdown').classList.toggle('show');
        });
        
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.profile-icon') && !event.target.matches('.fa-user')) {
                var dropdown = document.getElementById('profileDropdown');
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        });
        
        function logout() {
            fetch('logout.php')
                .finally(() => {
                    window.location.href = "registration.php";
                });
        }
        
        function showSettingsForm() {
            window.location.href = "sports.php";
        }
    </script>
</body>
</html>
<?php
// Close connection
$conn->close();
?>