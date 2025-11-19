<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
$category = isset($_GET['category']) ? $_GET['category'] : '';
$events = [];
$games = [];
$categories = [];
$student_id = "";
$student_name = "";
$student_reg = "";
$student_dept = "";
$tournaments = [];
$logged_in = false;

if (isset($_SESSION['reg_number']) && !empty($_SESSION['reg_number'])) {
    $logged_in = true;
    $student_reg = $_SESSION['reg_number'];
    
    $sql_student = "SELECT id, name, reg_number, department FROM students WHERE reg_number = ?";
    $stmt = $conn->prepare($sql_student);
    $stmt->bind_param("s", $student_reg);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
        $student_id = $student_data['id'];
        $student_name = $student_data['name'];
        $student_dept = $student_data['department'];
        
        $_SESSION['student_id'] = $student_id;
        $_SESSION['student_name'] = $student_name;
        $_SESSION['student_dept'] = $student_dept;
    }
} else {
    header("Location: registration.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: registration.php");
    exit;
}

if ($logged_in && !empty($student_reg)) {
    $reg_sql = "SELECT id, event, event_category, game, game_category, payment_status 
                FROM singleplayer
                WHERE reg_number = ? 
                ORDER BY event, game";
    $reg_stmt = $conn->prepare($reg_sql);
    $reg_stmt->bind_param("s", $student_reg);
    $reg_stmt->execute();
    $reg_result = $reg_stmt->get_result();
    
    if ($reg_result && $reg_result->num_rows > 0) {
        while ($row = $reg_result->fetch_assoc()) {
            $row['type'] = 'single';
            $my_registrations[] = $row;
            
            if (!in_array($row['event'], $events)) {
                $events[] = $row['event'];
            }
        }
    }

    $group_reg_sql = "SELECT id, name, reg_number, email, phone, department, section, event, event_category, 
                     game_category, game, team_member_name, team_member_reg_number, amount, payment_status, 
                     group_id, registration_date, transaction_id, payment_date 
                     FROM groupplayers
                     WHERE reg_number = ? OR team_member_reg_number LIKE ?
                     ORDER BY event, game";
    $group_reg_stmt = $conn->prepare($group_reg_sql);
    $search_param = "%$student_reg%";
    $group_reg_stmt->bind_param("ss", $student_reg, $search_param);
    $group_reg_stmt->execute();
    $group_reg_result = $group_reg_stmt->get_result();
    
    if ($group_reg_result && $group_reg_result->num_rows > 0) {
        while ($row = $group_reg_result->fetch_assoc()) {
            $row['type'] = 'group';
            $my_group_registrations[] = $row;
            $my_registrations[] = $row;
            
            if (!in_array($row['event'], $events)) {
                $events[] = $row['event'];
            }
        }
    }

    if (empty($my_registrations)) {
        $message = '<div class="alert alert-info">You have not registered for any events yet.</div>';
    }
}

if (isset($_GET['event']) && !empty($_GET['event'])) {
    $event = $conn->real_escape_string($_GET['event']);
    $games = [];
    
    foreach ($my_registrations as $reg) {
        if ($reg['event'] == $event && !in_array($reg['game'], $games)) {
            $games[] = $reg['game'];
        }
    }
    
    sort($games);
}

if (isset($_GET['event']) && !empty($_GET['event']) && isset($_GET['game']) && !empty($_GET['game'])) {
    $event = $conn->real_escape_string($_GET['event']);
    $game = $conn->real_escape_string($_GET['game']);
    $categories = [];
    
    foreach ($my_registrations as $reg) {
        if ($reg['event'] == $event && $reg['game'] == $game && !empty($reg['game_category']) && !in_array($reg['game_category'], $categories)) {
            $categories[] = $reg['game_category'];
        }
    }
    
    sort($categories);
}

if ($logged_in && !empty($student_reg)) {
    $sql_matchups = "SELECT m.* FROM matchups m 
                    WHERE m.player1_reg = ? OR m.player2_reg = ?";
                    
    $params = [$student_reg, $student_reg];
    $types = "ss";
    
    if (isset($_GET['event']) && !empty($_GET['event'])) {
        $event = $_GET['event'];
        $sql_matchups .= " AND m.event = ?";
        $params[] = $event;
        $types .= "s";
        
        if (isset($_GET['game']) && !empty($_GET['game'])) {
            $game = $_GET['game'];
            $sql_matchups .= " AND m.game = ?";
            $params[] = $game;
            $types .= "s";
            
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category = $_GET['category'];
                $sql_matchups .= " AND m.category = ?";
                $params[] = $category;
                $types .= "s";
            }
        }
    }
    
    $sql_matchups .= " ORDER BY m.event, m.game, m.round, m.match_number";
    
    $stmt_matchups = $conn->prepare($sql_matchups);
    $stmt_matchups->bind_param($types, ...$params);
    $stmt_matchups->execute();
    $result_matchups = $stmt_matchups->get_result();
    
    if ($result_matchups && $result_matchups->num_rows > 0) {
        while ($row = $result_matchups->fetch_assoc()) {
            $view_matchups[] = $row;
            $my_matchups[] = $row;
            
            if ($row['status'] == 'pending' || $row['status'] == 'in_progress') {
                $my_upcoming_matchups[] = $row;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournaments - SRM Sports Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>body{font-family:Arial,sans-serif;background-color:#f7f7f7;margin:0;padding:0;text-align:center}.container{width:95%;margin:20px auto;padding:20px;background:white;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1)}.header{background-color:#484622;color:white;padding:15px 0;margin-bottom:20px;border-radius:5px 5px 0 0}.header h1{margin:0;font-size:1.8rem}.form-group{margin-bottom:15px;text-align:left}.form-group label{display:inline-block;width:120px;text-align:right;margin-right:10px;font-weight:bold}.form-control{padding:8px;border-radius:4px;border:1px solid #ddd;width:250px}.btn{padding:8px 16px;margin:0 5px;cursor:pointer;border:none;border-radius:5px;font-size:15px}.btn-primary{background:#484622;color:white}.btn-primary:hover{background:#5d5b2d}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268}.alert{padding:10px;margin:15px 0;border-radius:5px}.alert-success{background-color:#dff0d8;color:#3c763d;border:1px solid #d6e9c6}.alert-danger{background-color:#f2dede;color:#a94442;border:1px solid #ebccd1}.alert-info{background-color:#d1ecf1;color:#0c5460;border:1px solid #bee5eb}.table-responsive{overflow-x:auto;margin-bottom:15px}table{width:100%;border-collapse:collapse;font-size:0.9rem;margin-bottom:20px}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background-color:#484622;color:white}tr:nth-child(even){background-color:#f2f2f2}tr:hover{background-color:#ddd}.highlight-row{background-color:#ffeeba!important}.tab-container{margin:20px 0}.tab{overflow:hidden;border:1px solid #ccc;background-color:#f1f1f1}.tab button{background-color:inherit;float:left;border:none;outline:none;cursor:pointer;padding:10px 16px;transition:0.3s}.tab button:hover{background-color:#ddd}.tab button.active{background-color:#484622;color:white}.tabcontent{display:none;padding:6px 12px;border:1px solid #ccc;border-top:none;animation:fadeEffect 1s}@keyframes fadeEffect{from{opacity:0}to{opacity:1}}.user-info{background-color:#e9ecef;padding:10px;border-radius:5px;margin-bottom:20px;text-align:left}.user-info p{margin:5px 0}.bracket{display:flex;justify-content:space-around;margin-bottom:30px}.round{display:flex;flex-direction:column;justify-content:space-around;width:200px}.round-title{text-align:center;font-weight:bold;margin-bottom:10px;background-color:#484622;color:white;padding:5px;border-radius:3px}.match{border:1px solid #ccc;border-radius:5px;margin:5px;padding:10px;background-color:white;box-shadow:0 1px 3px rgba(0,0,0,0.1)}.match-title{font-size:0.8rem;color:#666;margin-bottom:5px}.player{padding:5px;border-radius:3px}.player1{border-bottom:1px solid #eee}.winner{font-weight:bold;background-color:#d4edda}.matchup-details{font-size:0.8rem;color:#666;margin-top:5px}.upcoming-match{border:2px solid #ffc107;background-color:#fff3cd}.completed-match{background-color:#f8f9fa}.my-match{border:2px solid #28a745}.status-badge{display:inline-block;padding:2px 6px;border-radius:3px;font-size:0.75rem;font-weight:bold;text-transform:uppercase}.status-pending{background-color:#ffc107;color:#333}.status-in_progress{background-color:#17a2b8;color:white}.status-completed{background-color:#28a745;color:white}.status-cancelled{background-color:#dc3545;color:white}.payment-status{display:inline-block;padding:3px 6px;border-radius:3px;font-size:0.8rem}.payment-paid{background-color:#d4edda;color:#155724}.payment-pending{background-color:#fff3cd;color:#856404}.nav-btn{display:inline-block;margin:10px 5px;padding:8px 15px;background-color:#484622;color:white;text-decoration:none;border-radius:5px;font-weight:bold}.nav-btn:hover{background-color:#5d5b2d}.nav-bar{margin-bottom:20px}.group-member{font-size:0.85rem;color:#333;margin-top:5px;padding:5px;background:#f9f9f9;border-radius:3px}.registration-type{display:inline-block;padding:2px 6px;border-radius:3px;font-size:0.7rem;font-weight:bold;margin-right:5px}.type-single{background-color:#17a2b8;color:white}.type-group{background-color:#28a745;color:white}.social-media{margin-top:15px;display:flex;justify-content:center;gap:15px}.social-media a{color:#fff;font-size:1.5rem;transition:color 0.3s}.social-media a:hover{color:#d1a74a}footer{background-color:#484622;color:white;text-align:center;padding:20px;margin-top:50px}header,footer{background-color:#484622;color:white;padding:15px 0;position:relative}header{display:flex;justify-content:space-between;align-items:center;padding:15px 30px}.header-title{font-size:24px;margin:0}.user-profile{position:relative;display:inline-block}.profile-icon{width:40px;height:40px;background-color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#484622;font-size:20px;transition:all 0.3s}.profile-icon:hover{background-color:#e0e0e0}.dropdown-menu{display:none;position:absolute;right:0;background-color:#fff;min-width:200px;box-shadow:0 8px 16px rgba(0,0,0,0.2);z-index:1;border-radius:8px;padding:10px 0;top:50px}.dropdown-menu.show{display:block}.dropdown-menu a{color:#333;padding:10px 15px;text-decoration:none;display:block;text-align:left;transition:background-color 0.3s}.dropdown-menu a:hover{background-color:#f1f1f1}.dropdown-menu .user-info{padding:10px 15px;border-bottom:1px solid #eee;margin-bottom:5px}.dropdown-menu .user-name{font-weight:bold;font-size:16px}.dropdown-menu .user-email{color:#666;font-size:14px}.logout-btn{background:none;border:none;width:100%;text-align:left;padding:10px 15px;color:#dc3545;cursor:pointer;font-size:15px;display:flex;align-items:center}.logout-btn:hover{background-color:#f1f1f1}.dropdown-menu a i,.logout-btn i{margin-right:8px;width:20px;text-align:center}.form-container button:hover{background-color:#357abd}.tabs{display:flex;margin-bottom:20px;border-bottom:1px solid #ddd}#settingsForm{display:none;width:450px;height:auto;min-height:450px;margin:20px auto;padding:30px;border:1px solid #e0e0e0;border-radius:10px;background-color:#ffffff;box-shadow:0 5px 15px rgba(0,0,0,0.1);box-sizing:border-box;overflow-y:auto}.tab{padding:10px 20px;cursor:pointer;transition:background-color 0.3s}.tab.active{background-color:#484622;color:white;border-radius:4px 4px 0 0}.tab-content{display:none}.tab-content.active{display:block}.form-group{margin-bottom:20px}.form-group label{display:block;margin-bottom:5px;font-weight:bold}.form-group input{width:100%;padding:10px;border:1px solid #ddd;border-radius:4px}.button-container{display:flex;gap:10px;margin-top:30px}.submit-button, .back-button{background-color:#4a90e2;color:white;cursor:pointer;font-weight:bold;transition:background-color 0.3s;flex:1;padding:12px 15px;border:none;border-radius:5px;font-size:16px;text-align:center;text-decoration:none;display:inline-block}.submit-button{background-color:#0066cc!important;color:white!important}.submit-button:hover{background-color:#0055aa!important}.back-button{background-color:#e74c3c!important;color:white!important}.back-button:hover{background-color:#c0392b!important}.alert{padding:10px;margin-bottom:20px;border-radius:4px}.alert-success{background-color:#d4edda;color:#155724}.alert-danger{background-color:#f8d7da;color:#721c24}.user-profile{position:relative;display:inline-block}.profile-icon{position:relative;font-size:24px;color:white;cursor:pointer;width:40px;height:40px;background-color:#5a582d;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background-color 0.3s;box-sizing:content-box}.profile-icon i{display:flex;align-items:center;justify-content:center;width:100%;height:100%}</style>
</head>
<body>
<header>
    <h1 class="header-title">Sports Portal</h1>
    <div class="user-profile">
        <div class="profile-icon" id="profileIcon"><i class="fas fa-user"></i></div>
        <div class="dropdown-menu" id="profileDropdown">
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($student_reg); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($student_name); ?></div>
            </div>
            <a href="my-registrations.php"><i class="fas fa-clipboard-list"></i> My Registrations</a>
            <a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
            <a href="volunteers.php"><i class="fas fa-hands-helping"></i> Volunteers</a>
            <a href="javascript:void(0);" onclick="showSettingsForm()"><i class="fas fa-cog"></i> Settings</a>
            <button type="button" class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>
    </div>
</header>
    
<div class="container">
    
    <?php echo $message; ?>
    
    <div class="user-info">
        <h3>Welcome, <?php echo htmlspecialchars($student_name); ?></h3>
        <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($student_reg); ?></p>
    </div>
    
    <div class="tab-container">
        <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'MyRegistrations')" id="defaultOpen">My Registrations</button>
            <button class="tablinks" onclick="openTab(event, 'MyMatches')">My Matches</button>
        </div>
        
        <div id="MyRegistrations" class="tabcontent">
            <h3>My Event Registrations</h3>
            
            <form method="get" action="">
                <div class="form-group">
                    <label for="event">Event:</label>
                    <select name="event" id="event" class="form-control" onchange="this.form.submit();">
                        <option value="">All Events</option>
                        <?php foreach ($events as $evt): ?>
                            <option value="<?php echo htmlspecialchars($evt); ?>" <?php echo (isset($_GET['event']) && $_GET['event'] == $evt) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($evt); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (!empty($games)): ?>
                <div class="form-group">
                    <label for="game">Game:</label>
                    <select name="game" id="game" class="form-control" onchange="this.form.submit();">
                        <option value="">All Games</option>
                        <?php foreach ($games as $gm): ?>
                            <option value="<?php echo htmlspecialchars($gm); ?>" <?php echo (isset($_GET['game']) && $_GET['game'] == $gm) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($gm); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($categories)): ?>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category" class="form-control" onchange="this.form.submit();">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </form>
            
            <?php
            $filtered_registrations = $my_registrations;
            if (isset($_GET['event']) && !empty($_GET['event'])) {
                $event_filter = $_GET['event'];
                $filtered_registrations = array_filter($filtered_registrations, function($reg) use ($event_filter) {
                    return $reg['event'] == $event_filter;
                });
                
                if (isset($_GET['game']) && !empty($_GET['game'])) {
                    $game_filter = $_GET['game'];
                    $filtered_registrations = array_filter($filtered_registrations, function($reg) use ($game_filter) {
                        return $reg['game'] == $game_filter;
                    });
                    
                    if (isset($_GET['category']) && !empty($_GET['category'])) {
                        $category_filter = $_GET['category'];
                        $filtered_registrations = array_filter($filtered_registrations, function($reg) use ($category_filter) {
                            return $reg['game_category'] == $category_filter;
                        });
                    }
                }
            }
            ?>
            
            <?php if (!empty($filtered_registrations)): ?>
            <div class="table-responsive">
                <table>
                    <tr>
                        <th>Type</th>
                        <th>Event</th>
                        <th>Event Category</th>
                        <th>Game</th>
                        <th>Game Category</th>
                        <th>Payment Status</th>
                    </tr>
                    <?php foreach ($filtered_registrations as $reg): ?>
                        <?php
                        $has_matchup = false;
                        $matchup_status = "Not scheduled";
                        
                        foreach ($my_matchups as $match) {
                            if ($match['event'] == $reg['event'] && $match['game'] == $reg['game'] && 
                                (empty($reg['game_category']) || $match['category'] == $reg['game_category'])) {
                                $has_matchup = true;
                                $matchup_status = ucfirst($match['status']);
                                break;
                            }
                        }
                        ?>
                        <tr>
                            <td>
                                <span class="registration-type type-<?php echo $reg['type']; ?>">
                                    <?php echo ucfirst($reg['type']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($reg['event']); ?></td>
                            <td><?php echo !empty($reg['event_category']) ? htmlspecialchars($reg['event_category']) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($reg['game']); ?></td>
                            <td><?php echo !empty($reg['game_category']) ? htmlspecialchars($reg['game_category']) : '-'; ?></td>
                            
                            <td>
                                <span class="payment-status payment-<?php echo strtolower($reg['payment_status']); ?>">
                                    <?php echo ucfirst($reg['payment_status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php else: ?>
            <p>No registrations found with the selected criteria.</p>
            <?php endif; ?>
        </div>
        
        <div id="MyMatches" class="tabcontent">
            <h3>My Tournament Matches</h3>
            
            <form method="get" action="">
                <div class="form-group">
                    <label for="event">Event:</label>
                    <select name="event" id="event" class="form-control" onchange="this.form.submit();">
                        <option value="">All Events</option>
                        <?php foreach ($events as $evt): ?>
                            <option value="<?php echo htmlspecialchars($evt); ?>" <?php echo (isset($_GET['event']) && $_GET['event'] == $evt) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($evt); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (!empty($games)): ?>
                <div class="form-group">
                    <label for="game">Game:</label>
                    <select name="game" id="game" class="form-control" onchange="this.form.submit();">
                        <option value="">All Games</option>
                        <?php foreach ($games as $gm): ?>
                            <option value="<?php echo htmlspecialchars($gm); ?>" <?php echo (isset($_GET['game']) && $_GET['game'] == $gm) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($gm); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($categories)): ?>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category" class="form-control" onchange="this.form.submit();">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </form>
            
            <?php if (!empty($my_matchups)): ?>
            <div class="table-responsive">
                <table>
                    <tr>
                        <th>Event</th>
                        <th>Game</th>
                        <?php if (!empty($categories)): ?>
                        <th>Category</th>
                        <?php endif; ?>
                        <th>Round</th>
                        <th>Match</th>
                        <th>Opponent</th>
                        <th>Status</th>
                        <th>Winner</th>
                        <th>Score</th>
                    </tr>
                    <?php foreach ($my_matchups as $match): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($match['event']); ?></td>
                            <td><?php echo htmlspecialchars($match['game']); ?></td>
                            <?php if (!empty($categories)): ?>
                            <td><?php echo htmlspecialchars($match['category'] ?? ''); ?></td>
                            <?php endif; ?>
                            <td><?php echo $match['round']; ?></td>
                            <td><?php echo $match['match_number']; ?></td>
                            <td>
                                <?php 
                                if ($match['player1_reg'] == $student_reg) {
                                    echo htmlspecialchars($match['player2_name']);
                                    if (!empty($match['player2_dept'])) {
                                        echo '<br><small>(' . htmlspecialchars($match['player2_dept']) . ')</small>';
                                    }
                                } else {
                                    echo htmlspecialchars($match['player1_name']);
                                    if (!empty($match['player1_dept'])) {
                                        echo '<br><small>(' . htmlspecialchars($match['player1_dept']) . ')</small>';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $match['status']; ?>">
                                    <?php echo ucfirst($match['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if (($match['winner_id'] == $match['player1_id'] && $match['player1_reg'] == $student_reg) || 
                                    ($match['winner_id'] == $match['player2_id'] && $match['player2_reg'] == $student_reg)) {
                                    echo "You";
                                } elseif ($match['winner_id'] > 0) {
                                    if ($match['player1_reg'] == $student_reg) {
                                        echo htmlspecialchars($match['player2_name']);
                                    } else {
                                        echo htmlspecialchars($match['player1_name']);
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td><?php echo !empty($match['score']) ? htmlspecialchars($match['score']) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php else: ?>
            <p>You do not have any scheduled matches with the selected criteria.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

    <div id="settingsForm" class="form-container">
        <h2>Account Settings</h2>
        <div class="tabs">
            <div class="tab active" onclick="showTab('changePasswordTab')">Change Password</div>
            <div class="tab" onclick="showTab('forgotPasswordTab')">Forgot Password</div>
        </div>
        <div id="changePasswordTab" class="tab-content active">
            <?php if (isset($password_message) && $password_message === "success"): ?>
                <div class="alert alert-success">Password updated successfully!</div>
                <script>
                    setTimeout(function() {
                        window.location.href = "registration.php";
                    }, 2000);
                </script>
            <?php elseif (isset($password_message) && !empty($password_message)): ?>
                <div class="alert alert-danger"><?php echo $password_message; ?></div>
            <?php endif; ?>
            <form id="changePasswordForm" method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" id="confirmPassword" name="confirm_password" required>
                </div>
                <div class="button-container">
                    <button type="submit" class="submit-button">Update Password</button>
                    <button type="button" class="back-button" onclick="goBack()">Back</button>
                </div>
            </form>
        </div>
        <div id="forgotPasswordTab" class="tab-content">
            <p>
                If you've forgotten your password or are unable to log in, please contact the administrator for assistance in resetting your password.<br>
                You can also try recovering your account using your registered Application Number or Registration Number.<br>
                For security reasons, direct password changes are not permitted through this portal.<br>
                Kindly reach out to the official support team or designated administrator with your registered details for verification and further help.<br>
            <br>📧 For technical assistance, email us at 
                <a href="mailto:sportsevent.helpdesk@srmap.edu.in">sportsevent.helpdesk@srmap.edu.in</a>.<br>
                🕐 Response time: within 24-48 working hours.
            </p>
            <div class="button-container">
                <button type="button" class="back-button" onclick="goBack()">Back</button>
            </div>
        </div>
    </div>

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
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    
    document.getElementById("defaultOpen").click();
    
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
            document.getElementById('mainContainer').style.display = 'none';
            document.getElementById('settingsForm').style.display = 'block';
            document.getElementById('profileDropdown').classList.remove('show');
        }
        
        function goBack() {
            document.getElementById('mainContainer').style.display = 'block';
            document.getElementById('settingsForm').style.display = 'none';
        }
        
        function showTab(tabId) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));
            
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>