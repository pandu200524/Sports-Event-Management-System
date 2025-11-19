<?php
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
$matchups = [];

// Get all distinct events
$sql_events = "SELECT DISTINCT event FROM singleplayer UNION SELECT DISTINCT event FROM groupplayers ORDER BY event";
$events_result = $conn->query($sql_events);
$events = [];
if ($events_result && $events_result->num_rows > 0) {
    while ($row = $events_result->fetch_assoc()) {
        $events[] = $row['event'];
    }
}

// Get all distinct games for the selected event
$games = [];
if (isset($_GET['event']) && !empty($_GET['event'])) {
    $event = $conn->real_escape_string($_GET['event']);
    $sql_games = "SELECT DISTINCT game FROM singleplayer WHERE event = '$event' UNION SELECT DISTINCT game FROM groupplayers WHERE event = '$event' ORDER BY game";
    $games_result = $conn->query($sql_games);
    if ($games_result && $games_result->num_rows > 0) {
        while ($row = $games_result->fetch_assoc()) {
            $games[] = $row['game'];
        }
    }
}

// Generate matchups when form is submitted
if (isset($_POST['generate'])) {
    $event = $conn->real_escape_string($_POST['event']);
    $game = $conn->real_escape_string($_POST['game']);
    $category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';
    $type = $conn->real_escape_string($_POST['type']);
    
    // Check if matchups already exist
    $check_sql = "SELECT COUNT(*) as count FROM matchups WHERE event = '$event' AND game = '$game'";
    if (!empty($category)) {
        $check_sql .= " AND category = '$category'";
    }
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();
    
    if ($check_row['count'] > 0 && !isset($_POST['overwrite'])) {
        $message = "<div class='alert alert-warning'>Matchups already exist for this selection. Check 'Overwrite existing' to regenerate.</div>";
    } else {
        // Clear existing matchups if overwrite is checked
        if (isset($_POST['overwrite'])) {
            $delete_sql = "DELETE FROM matchups WHERE event = '$event' AND game = '$game'";
            if (!empty($category)) {
                $delete_sql .= " AND category = '$category'";
            }
            $conn->query($delete_sql);
        }
        
        // Fetch participants
        if ($type == 'single') {
            $sql = "SELECT id, name, reg_number, department, game_category FROM singleplayer WHERE event = '$event' AND game = '$game'";
            if (!empty($category)) {
                $sql .= " AND game_category = '$category'";
            }
        } else {
            $sql = "SELECT id, name, reg_number, department, game_category FROM groupplayers WHERE event = '$event' AND game = '$game' AND (group_id IS NULL OR group_id = 0)";
            if (!empty($category)) {
                $sql .= " AND game_category = '$category'";
            }
        }
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $players = [];
            while ($row = $result->fetch_assoc()) {
                $players[] = $row;
            }
            
            // Shuffle the players to randomize matchups
            shuffle($players);
            
            // Generate tournament bracket
            if (count($players) > 1) {
                // Calculate number of rounds needed based on player count
                $num_players = count($players);
                $total_rounds = ceil(log($num_players, 2));
                $total_matches = pow(2, $total_rounds) - 1;
                
                // Calculate number of byes needed
                $complete_bracket_size = pow(2, $total_rounds);
                $byes_needed = $complete_bracket_size - $num_players;
                
                // First round matches
                $round = 1;
                $match_id = 1;
                $first_round_matches = $num_players - $byes_needed;
                $first_round_matches = $first_round_matches / 2;
                
                // Players who get a bye directly to round 2
                $bye_players = array_slice($players, $first_round_matches * 2);
                
                // Players who play in round 1
                $active_players = array_slice($players, 0, $first_round_matches * 2);
                
                // Create first round matches
for ($i = 0; $i < $first_round_matches; $i++) {
    $player1 = $active_players[$i * 2];
    $player2 = $active_players[$i * 2 + 1];
    
    // Insert matchup into database - FIXED the parameter order
    $sql = "INSERT INTO matchups (event, game, category, player1_id, player1_name, player1_reg, player1_dept, 
            player2_id, player2_name, player2_reg, player2_dept, round, match_number, type, next_match_id) 
            VALUES ('$event', '$game', '$category', '{$player1['id']}', '{$player1['name']}', '{$player1['reg_number']}', 
            '{$player1['department']}', '{$player2['id']}', '{$player2['name']}', '{$player2['reg_number']}', 
            '{$player2['department']}', $round, $match_id, '$type', NULL)";
    
    $conn->query($sql);
    
    // Store for display
    $matchups[] = [
        'round' => $round,
        'match' => $match_id,
        'player1' => $player1,
        'player2' => $player2
    ];
    
    $match_id++;
}
                // Create placeholder matches for subsequent rounds
                $current_round = 2;
                $players_in_round = $first_round_matches + count($bye_players);
                $matches_in_round = $players_in_round / 2;
                
                // Add bye players directly to round 2
                $bye_player_index = 0;
                
                while ($current_round <= $total_rounds) {
                    for ($i = 0; $i < $matches_in_round; $i++) {
                        if ($current_round == 2 && $bye_player_index < count($bye_players)) {
                            // For round 2, some players might get a bye from round 1
                            $player1 = $bye_players[$bye_player_index];
                            $player1_id = $player1['id'];
                            $player1_name = $player1['name'];
                            $player1_reg = $player1['reg_number'];
                            $player1_dept = $player1['department'];
                            $bye_player_index++;
                            
                            // Second player will be filled in later as winner of a round 1 match
                            $player2_id = NULL;
                            $player2_name = "TBD (Winner of Round 1)";
                            $player2_reg = "";
                            $player2_dept = "";
                        } else {
                            // Both players will be determined by previous matches
                            $player1_id = NULL;
                            $player1_name = "TBD";
                            $player1_reg = "";
                            $player1_dept = "";
                            $player2_id = NULL;
                            $player2_name = "TBD";
                            $player2_reg = "";
                            $player2_dept = "";
                        }
                        
                        // Insert placeholder match
                        $sql = "INSERT INTO matchups (event, game, category, player1_id, player1_name, player1_reg, player1_dept, 
                                player2_id, player2_name, player2_reg, player2_dept, round, match_number, type, next_match_id) 
                                VALUES ('$event', '$game', '$category', ";
                        
                        $sql .= $player1_id ? "$player1_id" : "NULL";
                        $sql .= ", '$player1_name', '$player1_reg', '$player1_dept', ";
                        $sql .= $player2_id ? "$player2_id" : "NULL";
                        $sql .= ", '$player2_name', '$player2_reg', '$player2_dept', ";
                        $sql .= "$current_round, $match_id, '$type', NULL)";
                        
                        $conn->query($sql);
                        $match_id++;
                    }
                    
                    // For next round
                    $current_round++;
                    $players_in_round = $matches_in_round;
                    $matches_in_round = $players_in_round / 2;
                }
                
                // Now update the next_match_id for all matches except the final
                // First, get all matches
                $sql = "SELECT id, round, match_number FROM matchups 
                       WHERE event = '$event' AND game = '$game'";
                if (!empty($category)) {
                    $sql .= " AND category = '$category'";
                }
                $sql .= " ORDER BY round, match_number";
                
                $all_matches = $conn->query($sql);
                $matches_by_round = [];
                
                if ($all_matches && $all_matches->num_rows > 0) {
                    while ($row = $all_matches->fetch_assoc()) {
                        if (!isset($matches_by_round[$row['round']])) {
                            $matches_by_round[$row['round']] = [];
                        }
                        $matches_by_round[$row['round']][] = $row;
                    }
                    
                    // For each round except the final one
                    for ($r = 1; $r < $total_rounds; $r++) {
                        if (isset($matches_by_round[$r])) {
                            foreach ($matches_by_round[$r] as $index => $match) {
                                // Calculate which match in the next round this feeds into
                                $next_round_index = floor($index / 2);
                                $next_match_id = $matches_by_round[$r + 1][$next_round_index]['id'];
                                
                                // Update the match
                                $update_sql = "UPDATE matchups SET next_match_id = $next_match_id WHERE id = {$match['id']}";
                                $conn->query($update_sql);
                            }
                        }
                    }
                }
                
                $message = "<div class='alert alert-success'>Successfully generated tournament bracket with " . count($matchups) . " initial matchups for " . count($players) . " participants.</div>";
            } else {
                $message = "<div class='alert alert-warning'>Not enough participants to create matchups. Need at least 2 participants.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>No participants found for the selected criteria.</div>";
        }
    }
}

// Get game categories when a game is selected
$categories = [];
if (isset($_GET['event']) && !empty($_GET['event']) && isset($_GET['game']) && !empty($_GET['game'])) {
    $event = $conn->real_escape_string($_GET['event']);
    $game = $conn->real_escape_string($_GET['game']);
    $sql_categories = "SELECT DISTINCT game_category FROM singleplayer WHERE event = '$event' AND game = '$game' AND game_category != '' 
                      UNION SELECT DISTINCT game_category FROM groupplayers WHERE event = '$event' AND game = '$game' AND game_category != '' 
                      ORDER BY game_category";
    $categories_result = $conn->query($sql_categories);
    if ($categories_result && $categories_result->num_rows > 0) {
        while ($row = $categories_result->fetch_assoc()) {
            $categories[] = $row['game_category'];
        }
    }
}

// View existing matchups
$view_matchups = [];
if (isset($_GET['view']) && $_GET['view'] == 'matchups') {
    $where_clause = "";
    if (isset($_GET['event']) && !empty($_GET['event'])) {
        $event = $conn->real_escape_string($_GET['event']);
        $where_clause .= " WHERE event = '$event'";
        
        if (isset($_GET['game']) && !empty($_GET['game'])) {
            $game = $conn->real_escape_string($_GET['game']);
            $where_clause .= " AND game = '$game'";
            
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $category = $conn->real_escape_string($_GET['category']);
                $where_clause .= " AND category = '$category'";
            }
        }
    }
    
    $sql = "SELECT * FROM matchups $where_clause ORDER BY round, match_number";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $view_matchups[] = $row;
        }
    }
}

// Create matchups table if it doesn't exist with next_match_id field
$sql = "CREATE TABLE IF NOT EXISTS matchups (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(100) NOT NULL,
    game VARCHAR(100) NOT NULL,
    category VARCHAR(100),
    player1_id INT(11),
    player1_name VARCHAR(100) NOT NULL,
    player1_reg VARCHAR(50),
    player1_dept VARCHAR(100),
    player2_id INT(11),
    player2_name VARCHAR(100) NOT NULL,
    player2_reg VARCHAR(50),
    player2_dept VARCHAR(100),
    round INT(11) NOT NULL,
    match_number INT(11) NOT NULL,
    winner_id INT(11) DEFAULT NULL,
    score VARCHAR(100) DEFAULT NULL,
    type VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    next_match_id INT(11) DEFAULT NULL
)";
$conn->query($sql);

// Determine which tab to display by default
$defaultTab = "Generate";
if (isset($_GET['view']) && $_GET['view'] == 'matchups') {
    $defaultTab = "View";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Matchups</title>
    <style>body{font-family:Arial,sans-serif;background-color:#f2f2f2;margin:0;padding:0;text-align:center}.container{width:98%;margin:10px auto;padding:10px;background:white;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);overflow-x:hidden}.header{background-color:#484622;color:white;padding:10px 0;margin-bottom:15px}.header h1{margin:0;font-size:1.8rem}.form-group{margin-bottom:15px}.form-group label{display:inline-block;width:150px;text-align:right;margin-right:10px}.form-control{padding:8px;border-radius:4px;border:1px solid #ddd;width:250px}.btn{padding:8px 16px;margin:0 8px;cursor:pointer;border:none;background:#484622;color:white;border-radius:5px;font-size:15px}.btn:hover{background:#5d5b2d}.alert{padding:10px;margin:15px 0;border-radius:5px}.alert-success{background-color:#dff0d8;color:#3c763d;border:1px solid #d6e9c6}.alert-warning{background-color:#fcf8e3;color:#8a6d3b;border:1px solid #faebcc}.alert-danger{background-color:#f2dede;color:#a94442;border:1px solid #ebccd1}.table-responsive{overflow-x:auto;width:100%;margin-bottom:15px}table{width:100%;border-collapse:collapse;font-size:0.85rem}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background-color:#484622;color:white}tr:nth-child(even){background-color:#f2f2f2}tr:hover{background-color:#ddd}.back-btn{margin-top:15px;margin-bottom:15px;display:inline-block;padding:8px 16px;background:rgb(210,152,44);color:white;text-decoration:none;border-radius:5px}.back-btn:hover{background:orange}h2{color:#333;margin:10px 0}.tab-container{margin:20px 0}.tab{overflow:hidden;border:1px solid #ccc;background-color:#f1f1f1}.tab button{background-color:inherit;float:left;border:none;outline:none;cursor:pointer;padding:10px 16px;transition:0.3s}.tab button:hover{background-color:#ddd}.tab button.active{background-color:#484622;color:white}.tabcontent{display:none;padding:6px 12px;border:1px solid #ccc;border-top:none;animation:fadeEffect 1s}@keyframes fadeEffect{from{opacity:0}to{opacity:1}}.checkbox-group{margin:10px 0}</style>
</head>
<body>
    <div class="header">
        <h1>Sports Portal Admin</h1>
    </div>

    <div class="container">
        <h2>Generate & View Matchups</h2>
        
        <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'Generate')" id="generateTab">Generate Matchups</button>
            <button class="tablinks" onclick="openTab(event, 'View')" id="viewTab">View Matchups</button>
        </div>
        
        <div id="Generate" class="tabcontent">
            <h3>Generate Tournament Bracket</h3>
            <?php echo $message; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="event">Event:</label>
                    <select name="event" id="event" class="form-control" required onchange="this.form.action='generate_matchups.php?event='+this.value; this.form.submit();">
                        <option value="">Select Event</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?php echo htmlspecialchars($event); ?>" <?php echo (isset($_GET['event']) && $_GET['event'] == $event) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($event); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="game">Game:</label>
                    <select name="game" id="game" class="form-control" required onchange="this.form.action='generate_matchups.php?event=<?php echo isset($_GET['event']) ? $_GET['event'] : ''; ?>&game='+this.value; this.form.submit();">
                        <option value="">Select Game</option>
                        <?php foreach ($games as $game): ?>
                            <option value="<?php echo htmlspecialchars($game); ?>" <?php echo (isset($_GET['game']) && $_GET['game'] == $game) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($game); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (!empty($categories)): ?>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="type">Player Type:</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="single">Single Player</option>
                        <option value="group">Group/Team</option>
                    </select>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="overwrite" name="overwrite">
                    <label for="overwrite">Overwrite existing matchups</label>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="generate" class="btn">Generate Tournament Bracket</button>
                </div>
            </form>
            
            <?php if (!empty($matchups)): ?>
                <h3>First Round Matchups</h3>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>Round</th>
                            <th>Match</th>
                            <th>Player/Team 1</th>
                            <th>Department</th>
                            <th>Player/Team 2</th>
                            <th>Department</th>
                        </tr>
                        <?php foreach ($matchups as $match): ?>
                            <tr>
                                <td><?php echo $match['round']; ?></td>
                                <td><?php echo $match['match']; ?></td>
                                <td><?php echo htmlspecialchars($match['player1']['name']) . ' (' . htmlspecialchars($match['player1']['reg_number']) . ')'; ?></td>
                                <td><?php echo htmlspecialchars($match['player1']['department']); ?></td>
                                <td><?php echo htmlspecialchars($match['player2']['name']) . ' (' . htmlspecialchars($match['player2']['reg_number']) . ')'; ?></td>
                                <td><?php echo htmlspecialchars($match['player2']['department']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="View" class="tabcontent">
            <h3>View Tournament Bracket</h3>
            
            <form method="get" action="">
                <input type="hidden" name="view" value="matchups">
                <div class="form-group">
                    <label for="event_view">Event:</label>
                    <select name="event" id="event_view" class="form-control" onchange="this.form.submit();">
                        <option value="">All Events</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?php echo htmlspecialchars($event); ?>" <?php echo (isset($_GET['event']) && $_GET['event'] == $event) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($event); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (isset($_GET['event']) && !empty($_GET['event'])): ?>
                <div class="form-group">
                    <label for="game_view">Game:</label>
                    <select name="game" id="game_view" class="form-control" onchange="this.form.submit();">
                        <option value="">All Games</option>
                        <?php foreach ($games as $game): ?>
                            <option value="<?php echo htmlspecialchars($game); ?>" <?php echo (isset($_GET['game']) && $_GET['game'] == $game) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($game); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (!empty($categories) && isset($_GET['game']) && !empty($_GET['game'])): ?>
                <div class="form-group">
                    <label for="category_view">Category:</label>
                    <select name="category" id="category_view" class="form-control" onchange="this.form.submit();">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </form>
            
            <?php if (!empty($view_matchups)): ?>
                <h4>Tournament Bracket</h4>
                <?php
                    // Group by rounds for better display
                    $matches_by_round = [];
                    foreach ($view_matchups as $match) {
                        if (!isset($matches_by_round[$match['round']])) {
                            $matches_by_round[$match['round']] = [];
                        }
                        $matches_by_round[$match['round']][] = $match;
                    }
                    
                    // Sort rounds in ascending order
                    ksort($matches_by_round);
                    
                    // Display each round
                    foreach ($matches_by_round as $round_num => $round_matches):
                ?>
                <h5>Round <?php echo $round_num; ?></h5>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>Match</th>
                            <th>Game</th>
                            <th>Player/Team 1</th>
                            <th>Department</th>
                            <th>Player/Team 2</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Winner</th>
                            <th>Score</th>
                            <th>Action</th>
                        </tr>
                        <?php foreach ($round_matches as $match): ?>
                            <tr>
                                <td><?php echo $match['match_number']; ?></td>
                                <td><?php echo htmlspecialchars($match['game']); ?></td>
                                <td>
                                    <?php
                                    if ($match['player1_id']) {
                                        echo htmlspecialchars($match['player1_name']) . (!empty($match['player1_reg']) ? ' (' . htmlspecialchars($match['player1_reg']) . ')' : '');
                                    } else {
                                        echo htmlspecialchars($match['player1_name']);
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($match['player1_dept']); ?></td>
                                <td>
                                    <?php
                                    if ($match['player2_id']) {
                                        echo htmlspecialchars($match['player2_name']) . (!empty($match['player2_reg']) ? ' (' . htmlspecialchars($match['player2_reg']) . ')' : '');
                                    } else {
                                        echo htmlspecialchars($match['player2_name']);
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($match['player2_dept']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($match['status'])); ?></td>
                                <td>
                                    <?php 
                                    if ($match['winner_id'] == $match['player1_id']) {
                                        echo htmlspecialchars($match['player1_name']);
                                    } elseif ($match['winner_id'] == $match['player2_id']) {
                                        echo htmlspecialchars($match['player2_name']);
                                    } else {
                                        echo 'Not decided';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($match['score'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="update_match.php?id=<?php echo $match['id']; ?>" class="btn" style="padding:3px 8px;font-size:12px;">Update</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No matchups found with the selected criteria.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    // Function to open a specific tab
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
        if (evt) {
            evt.currentTarget.className += " active";
        } else {
            // If no event is provided, find the button by ID and make it active
            document.getElementById(tabName === "Generate" ? "generateTab" : "viewTab").className += " active";
        }
    }
    
    // When the page loads, check which tab should be active
    document.addEventListener("DOMContentLoaded", function() {
        openTab(null, "<?php echo $defaultTab; ?>");
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>