<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "sports";
$port = 3307;
try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname, $port);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed. Please try again later.");
    }
    $sql = "SELECT 
                id, 
                name, 
                start_date, 
                end_date, 
                location, 
                cover_image_url, 
                image_url, 
                is_payable,
                tag 
            FROM event 
            ORDER BY id ASC"; 
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching events: " . $conn->error);
    }
    $events = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
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
    <title>Events - SRM Sports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body{font-family:'Arial',sans-serif;margin:0;padding:0;background-color:#f4f4f4;color:#333}
    .admin-header{background-color:#484622;padding:10px 20px;display:flex;justify-content:space-between;align-items:center}
    .admin-header span{color:white;font-weight:bold}
    .admin-actions a{background-color:#ff9800;color:white;padding:8px 15px;border-radius:4px;text-decoration:none;margin-left:10px;transition:background-color 0.3s}
    .admin-actions a:hover{background-color:#e68a00}
    .header{background-color:#484622;color:white;padding:30px 0;text-align:center}
    .container{max-width:1200px;margin:0 auto;padding:20px}
    .events-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:25px;margin-top:30px}
    .event-card{background:white;border-radius:8px;overflow:hidden;box-shadow:0 4px 8px rgba(0,0,0,0.1);transition:all 0.3s ease}
    .event-card:hover{transform:translateY(-5px);box-shadow:0 6px 12px rgba(0,0,0,0.15)}
    .event-image{width:100%;height:200px;object-fit:cover}
    .placeholder-image{width:100%;height:200px;background-color:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#999}
    .event-info{padding:20px}
    .event-title{margin:0 0 10px;color:#484622;font-size:1.2rem}
    .event-tag{color:#666;font-size:0.9rem;margin-bottom:10px;font-style:italic}
    .event-dates{font-size:0.9rem;color:#555;margin-bottom:8px}
    .event-location{font-size:0.9rem;color:#555;margin-bottom:15px}
    .view-btn{display:inline-block;background-color:#484622;color:white;padding:8px 15px;border-radius:4px;text-decoration:none;margin-top:10px;font-size:0.9rem;transition:background-color 0.3s}
    .social-media{margin-top:15px;display:flex;justify-content:center;gap:15px}
    .social-media a{color:#fff;font-size:1.5rem;transition:color 0.3s}
    .social-media a:hover{color:#d1a74a}
    .view-btn:hover{background-color:#3a3a1e}
    .error-message{text-align:center;padding:50px;color:#dc3545;background-color:#ffecec;border-radius:8px;margin:20px 0}
    .no-events{text-align:center;padding:50px;color:#666;background-color:#f9f9f9;border-radius:8px;margin:20px 0}
    footer{background-color:#484622;color:white;text-align:center;padding:20px;margin-top:50px}
    @media (max-width:768px){.events-grid{grid-template-columns:1fr}.admin-header{flex-direction:column;gap:10px}.admin-actions{display:flex;flex-direction:column;gap:10px}.admin-actions a{margin:0;width:100%;text-align:center}}</style>
</head>
<body>
    <?php if (isset($_SESSION['admin_username'])): ?>
    <?php endif; ?>
    
    <div class="header">
        <h1>SRM Sports Events</h1>
        <p>Discover upcoming sports competitions</p>
    </div>
    
    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <h3><i class="fas fa-exclamation-triangle"></i> Error</h3>
                <p><?php echo htmlspecialchars($error_message); ?></p>
                <a href="dashboard.php" class="view-btn">Return to Dashboard</a>
            </div>
        <?php elseif (empty($events)): ?>
            <div class="no-events">
                <h3><i class="far fa-calendar-times"></i> No Events</h3>
                <p>There are currently no events in the database. Please check back later.</p>
                <?php if (isset($_SESSION['admin_username'])): ?>
                    <a href="add.php" class="view-btn">Create New Event</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <?php if (!empty($event['cover_image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($event['cover_image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['name']); ?>" 
                                 class="event-image"
                                 onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 300 200\"%3E%3Crect width=\"300\" height=\"200\" fill=\"%23f0f0f0\"/%3E%3Ctext x=\"150\" y=\"100\" font-family=\"Arial\" font-size=\"16\" text-anchor=\"middle\" fill=\"%23999\"%3ENo Image Available%3C/text%3E%3C/svg%3E'">
                        <?php else: ?>
                            <div class="placeholder-image">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="event-info">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['name']); ?></h3>
                            <?php if (!empty($event['tag'])): ?>
                                <p class="event-tag"><?php echo htmlspecialchars($event['tag']); ?></p>
                            <?php endif; ?>
                            
                            <p class="event-dates">
                                <i class="far fa-calendar-alt"></i> 
                                <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                <?php if (!empty($event['end_date']) && $event['end_date'] != $event['start_date']): ?>
                                    - <?php echo date('M j, Y', strtotime($event['end_date'])); ?>
                                <?php endif; ?>
                            </p>
                            
                            <?php if (!empty($event['location'])): ?>
                                <p class="event-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($event['location']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="view-btn">
                                <i class="fas fa-info-circle"></i> View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> SRM Sports. All rights reserved.</p>
        <div class="social-media">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
            <a href="https://www.youtube.com/c/SRMUniversityAP" target="_blank">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        });
    </script>
</body>
</html>