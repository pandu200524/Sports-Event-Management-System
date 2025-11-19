<?php
session_start();
// Database Connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "sports";
$port = 3307;
// Initialize variables
$error_message = '';
$event = null;

try {
    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname, $port);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed. Please try again later.");
    }

    // Validate event ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: events.php");
        exit();
    }

    if (!is_numeric($_GET['id'])) {
        throw new Exception("Invalid event ID format. Please provide a numeric ID.");
    }

    $event_id = intval($_GET['id']);
    
    // Get event details - Make sure all columns are properly fetched
    $stmt = $conn->prepare("SELECT 
        id, name, tag, overview, highlights, cover_image_url, image_url, indoor_sports, outdoor_sports, racket_sports,
        rules, about, start_date, end_date, location, contact, is_payable
        FROM event WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        throw new Exception("The requested event could not be found.");
    }

    $event = $result->fetch_assoc();
    
    // Close statement
    $stmt->close();
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
} finally {
    // Close connection
    if (isset($conn)) {
        $conn->close();
    }
}

// Determine if this is Udgam event (case-insensitive check)
$is_udgam = isset($event['name']) && strtolower($event['name']) === 'udgam';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($event) ? htmlspecialchars($event['name']) : 'Event Details'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #605c5c;
        }
        
        .header {
            text-align: center;
            padding: 30px;
            background-color: #484622;
            color: white;
            position: relative;
        }
        
        .admin-actions {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .admin-btn {
            background-color: #ff9800;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-left: 10px;
        }
        
        .admin-btn:hover {
            background-color: #e68a00;
        }
        
        footer {
            text-align: center;
            padding: 10px;
            background-color: #484622;
            color: white;
            margin-top: 20px;
        }
        
        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container a {
            background-color: rgb(210, 152, 44);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 10px;
            display: inline-block;
        }
        
        .button-container a:hover {
            background-color: orange;
        }
        
        .pay-button {
            background-color: #4CAF50 !important;
        }
        
        .pay-button:hover {
            background-color: #45a049 !important;
        }
        
        .overview-container {
            display: flex;
            align-items: flex-start;
            background-color: #fff;
            padding: 20px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            gap: 40px;
            margin: 20px;
        }
        
        .text {
            flex: 1;
            padding-right: 20px;
        }
        
        .event-image-container {
            width: 400px;
            height: 250px;
            margin-top: 20px;
        }
        
        .event-image-container img {
            width: 100%;
            height: 100%;
            margin-top: 20px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .sports-container {
            background-color: #ffecb3;
            border: 1px solid #ddd;
            padding: 20px;
            margin: 20px;
        }
        
        .sports-container h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .sports-categories {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .sports-category {
            flex: 1;
            min-width: 250px;
            padding: 0 15px;
            margin-bottom: 20px;
        }
        
        .sports-category h4 {
            color: #605c5c;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .sports-category ul {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
        }
        
        .sports-category li {
            margin-bottom: 8px;
        }
        
        .event-details-container {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px 40px;
            border-radius: 4px;
            margin: 20px;
        }
        
        .overview-container ul {
            padding-left: 20px;
            list-style-type: none;
        }
        
        .overview-container li:before {
            content: "•";
            margin-right: 5px;
            color: #ffa726;
            font-weight: bold;
        }
        
        .error-message {
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
            padding: 20px;
            margin: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .overview-container {
                flex-direction: column;
            }
            
            .event-image-container {
                width: 100%;
                height: auto;
            }
            
            .sports-category {
                min-width: 100%;
            }
            
            .admin-actions {
                position: static;
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>

    <?php if (isset($error_message) && !empty($error_message)): ?>
        <div class="error-message">
            <h2>Error</h2>
            <p><?php echo htmlspecialchars($error_message); ?></p>
            <a href="events.php" class="back-button">Back to Events</a>
        </div>
    <?php elseif (isset($event)): ?>
        <div class="header">
            <h1><?php echo htmlspecialchars($event['name']); ?></h1>
            <p><?php echo isset($event['tag']) ? htmlspecialchars($event['tag']) : ''; ?></p>
            
            <?php if (isset($_SESSION['admin_username'])): ?>
                <div class="admin-actions">
                    <!-- Admin actions can go here -->
                </div>
            <?php endif; ?>
        </div>

        <div class="overview-container">
            <div class="text">
                <p><strong>Overview:</strong> <?php echo isset($event['overview']) ? nl2br(htmlspecialchars($event['overview'])) : 'No overview available'; ?></p>
                <h3>Event Highlights:</h3>
                <ul>
                    <?php 
                    $highlights = (isset($event['highlights']) && !empty($event['highlights'])) ? explode("\n", $event['highlights']) : [];
                    if (!empty($highlights)): 
                        foreach ($highlights as $highlight): 
                            if(trim($highlight) !== ''): 
                    ?>
                            <li><?php echo htmlspecialchars($highlight); ?></li>
                    <?php 
                            endif;
                        endforeach;
                    else: 
                    ?>
                        <li>No highlights listed</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="event-image-container">
                <!-- Display Image from image_url -->
                <?php if (isset($event['image_url']) && !empty($event['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="Image of <?php echo htmlspecialchars($event['name']); ?>" style="margin-top: 20px;">
                <?php else: ?>
                    <div style="background-color: #ddd; height: 100%; display: flex; align-items: center; justify-content: center; margin-top: 20px;">
                        <p>No image available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="sports-container">
            <h3>Sports Conducted</h3>
            <div class="sports-categories">
                <div class="sports-category">
                    <h4>Indoor Sports</h4>
                    <ul>
                        <?php 
                        $indoor_sports = (isset($event['indoor_sports']) && !empty($event['indoor_sports'])) ? json_decode($event['indoor_sports']) : [];
                        if (!empty($indoor_sports) && is_array($indoor_sports)): 
                            foreach ($indoor_sports as $sport): 
                        ?>
                            <li><?php echo htmlspecialchars($sport); ?></li>
                        <?php 
                            endforeach;
                        else: 
                        ?>
                            <li>No indoor sports listed</li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="sports-category">
                    <h4>Outdoor Sports</h4>
                    <ul>
                        <?php 
                        $outdoor_sports = (isset($event['outdoor_sports']) && !empty($event['outdoor_sports'])) ? json_decode($event['outdoor_sports']) : [];
                        if (!empty($outdoor_sports) && is_array($outdoor_sports)): 
                            foreach ($outdoor_sports as $sport): 
                        ?>
                            <li><?php echo htmlspecialchars($sport); ?></li>
                        <?php 
                            endforeach;
                        else: 
                        ?>
                            <li>No outdoor sports listed</li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="sports-category">
                    <h4>Racket Games</h4>
                    <ul>
                        <?php 
                        $racket_sports = (isset($event['racket_sports']) && !empty($event['racket_sports'])) ? json_decode($event['racket_sports']) : [];
                        if (!empty($racket_sports) && is_array($racket_sports)): 
                            foreach ($racket_sports as $sport): 
                        ?>
                            <li><?php echo htmlspecialchars($sport); ?></li>
                        <?php 
                            endforeach;
                        else: 
                        ?>
                            <li>No racket games listed</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="event-details-container">
            <?php if (isset($event['rules']) && !empty($event['rules'])): ?>
                <h3>Rules:</h3>
                <ul>
                    <?php 
                    $rules = json_decode($event['rules']);
                    if (!empty($rules) && is_array($rules)):
                        foreach ($rules as $rule): 
                    ?>
                        <li><?php echo htmlspecialchars($rule); ?></li>
                    <?php 
                        endforeach; 
                    else:
                    ?>
                        <li>No rules specified</li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

            <?php if (isset($event['about']) && !empty($event['about'])): ?>
                <h3>About:</h3>
                <p><?php echo nl2br(htmlspecialchars($event['about'])); ?></p>
            <?php endif; ?>

            <h3>Event Details</h3>
            <p><strong>Start Date:</strong> <?php echo isset($event['start_date']) ? htmlspecialchars($event['start_date']) : 'Not specified'; ?></p>
            <p><strong>End Date:</strong> <?php echo isset($event['end_date']) ? htmlspecialchars($event['end_date']) : 'Not specified'; ?></p>
            <p><strong>Location:</strong> <?php echo isset($event['location']) ? htmlspecialchars($event['location']) : 'Not specified'; ?></p>
            <p><strong>Contact:</strong> <?php echo isset($event['contact']) ? htmlspecialchars($event['contact']) : 'Not specified'; ?></p>
            
            <div class="button-container">
                <a href="events.php"><i class="fas fa-arrow-left"></i> Back to Events</a>
                <?php if ($is_udgam || (isset($event['is_payable']) && $event['is_payable'] == 1)): ?>
                    <a href="registration.php?event=<?php echo urlencode(strtolower($event['name'])); ?>" class="pay-button"><i class="fas fa-ticket-alt"></i> Register & Pay</a>
                <?php else: ?>
                    <a href="registration.php?event=<?php echo urlencode(strtolower($event['name'])); ?>"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sports Events. All rights reserved.</p>
        </footer>
    <?php endif; ?>
</body>
</html>