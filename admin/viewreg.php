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
$singleTableStyle = "display: none;";
$groupTableStyle = "display: none;";
$volunteersTableStyle = "display: none;";

if (isset($_GET['view']) && $_GET['view'] === 'single') {
    $singleTableStyle = "display: block;";
} else if (isset($_GET['view']) && $_GET['view'] === 'group') {
    $groupTableStyle = "display: block;";
} else if (isset($_GET['view']) && $_GET['view'] === 'volunteers') {
    $volunteersTableStyle = "display: block;";
}

$eventFilter = isset($_GET['event']) ? $_GET['event'] : '';
$gameFilter = isset($_GET['game']) ? $_GET['game'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$committeeFilter = isset($_GET['committee']) ? $_GET['committee'] : '';

// Define the missing function
function highlightSearchTerm($text, $term) {
    if (empty($term)) {
        return $text;
    }
    
    // Case-insensitive replacement
    return preg_replace('/(' . preg_quote($term, '/') . ')/i', '<span class="search-highlight">$1</span>', $text);
}

// Modified single player query that uses GROUP BY to eliminate duplicates
$sql_single = "SELECT s.id, s.name, s.reg_number, s.email, s.phone, s.department, s.section, s.event, 
                s.event_category, s.game_category, s.game, 
                COALESCE(p.amount, 0) as amount, 
                CASE WHEN p.payment_status = 'paid' THEN 'Completed' ELSE 'Pending' END as payment_status,
                MAX(s.registration_date) as registration_date, 
                MAX(p.transaction_id) as transaction_id, 
                MAX(p.payment_date) as payment_date
              FROM singleplayer s
              LEFT JOIN payments p ON s.id = p.registration_id AND p.registration_type = 'single'";
              
// Build WHERE clause based on filters
$where_conditions = [];
if (!empty($eventFilter)) {
    $where_conditions[] = "s.event = '" . $conn->real_escape_string($eventFilter) . "'";
}
if (!empty($gameFilter)) {
    $where_conditions[] = "s.game = '" . $conn->real_escape_string($gameFilter) . "'";
}
if (!empty($searchTerm)) {
    $where_conditions[] = "(s.name LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR 
                           s.reg_number LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                           s.email LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                           s.phone LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                           s.department LIKE '%" . $conn->real_escape_string($searchTerm) . "%')";
}

if (!empty($where_conditions)) {
    $sql_single .= " WHERE " . implode(" AND ", $where_conditions);
}

// Group by the fields that determine uniqueness (excluding dates and transaction details)
$sql_single .= " GROUP BY s.reg_number, s.email, s.phone, s.department, s.section, s.event, s.game, s.game_category, s.event_category
                 ORDER BY s.id ASC";

$result_single = $conn->query($sql_single);

$sql_single_events = "SELECT DISTINCT event FROM singleplayer ORDER BY event ASC";
$result_single_events = $conn->query($sql_single_events);

$sql_single_games = "SELECT DISTINCT game FROM singleplayer ORDER BY game ASC";
$result_single_games = $conn->query($sql_single_games);

$sql_group = "SELECT id, name, reg_number, email, phone, department, section, event, event_category, game_category, game, team_member_name, team_member_reg_number, amount, payment_status, group_id, registration_date, transaction_id, payment_date FROM groupplayers WHERE group_id IS NULL OR group_id = 0";

// Build WHERE clause for group players
$group_where_conditions = [];
if (!empty($eventFilter)) {
    $group_where_conditions[] = "event = '" . $conn->real_escape_string($eventFilter) . "'";
}
if (!empty($gameFilter)) {
    $group_where_conditions[] = "game = '" . $conn->real_escape_string($gameFilter) . "'";
}
if (!empty($searchTerm)) {
    $group_where_conditions[] = "(name LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR 
                               reg_number LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                               email LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                               phone LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                               department LIKE '%" . $conn->real_escape_string($searchTerm) . "%')";
}

if (!empty($group_where_conditions)) {
    $sql_group .= " AND " . implode(" AND ", $group_where_conditions);
}

$sql_group .= " ORDER BY id ASC";
$result_group = $conn->query($sql_group);

$sql_group_events = "SELECT DISTINCT event FROM groupplayers ORDER BY event ASC";
$result_group_events = $conn->query($sql_group_events);

$sql_group_games = "SELECT DISTINCT game FROM groupplayers ORDER BY game ASC";
$result_group_games = $conn->query($sql_group_games);

// Updated volunteers query to include committee and residence columns
$sql_volunteers = "SELECT volunteer_id, name, email, phone, reg_number, branch, year, residence, committee, experience, ideas, improvements, event_id, event_name FROM volunteers";

// Build WHERE clause for volunteers
$volunteer_where_conditions = [];
if (!empty($eventFilter)) {
    $volunteer_where_conditions[] = "event_name = '" . $conn->real_escape_string($eventFilter) . "'";
}
if (!empty($committeeFilter)) {
    $volunteer_where_conditions[] = "committee = '" . $conn->real_escape_string($committeeFilter) . "'";
}
if (!empty($searchTerm)) {
    $volunteer_where_conditions[] = "(name LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR 
                                    reg_number LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                                    email LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                                    phone LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                                    branch LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR
                                    committee LIKE '%" . $conn->real_escape_string($searchTerm) . "%')";
}

if (!empty($volunteer_where_conditions)) {
    $sql_volunteers .= " WHERE " . implode(" AND ", $volunteer_where_conditions);
}

$sql_volunteers .= " ORDER BY volunteer_id ASC";
$result_volunteers = $conn->query($sql_volunteers);

$sql_volunteer_events = "SELECT DISTINCT event_name FROM volunteers ORDER BY event_name ASC";
$result_volunteer_events = $conn->query($sql_volunteer_events);

$sql_volunteer_committees = "SELECT DISTINCT committee FROM volunteers ORDER BY committee ASC";
$result_volunteer_committees = $conn->query($sql_volunteer_committees);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registrations</title>
    <style>
        body{font-family:Arial,sans-serif;background-color:#f2f2f2;margin:0;padding:0;text-align:center}
        .container{width:98%;margin:10px auto;padding:10px;background:white;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);overflow-x:hidden}
        .btn-container{display:flex;justify-content:center;margin-bottom:15px}
        .btn{padding:8px 16px;margin:0 8px;cursor:pointer;border:none;background:#484622;color:white;border-radius:5px;font-size:15px;text-decoration:none}
        .btn:hover{background:rgb(210,152,44);}
        .table-responsive{overflow-x:auto;width:100%;margin-bottom:15px}
        table{width:100%;border-collapse:collapse;font-size:0.85rem;white-space:nowrap;table-layout:auto}
        @media (min-width:992px){table{font-size:0.9rem}}
        th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}
        th{background-color:#484622;color:white;position:sticky;top:0}
        tr:nth-child(even){background-color:#f2f2f2}
        tr:hover{background-color:#ddd}
        .back-btn{margin-top:15px;margin-bottom:15px;display:inline-block;padding:8px 16px;background:rgb(210,152,44);color:white;text-decoration:none;border-radius:5px}
        .back-btn:hover{background:orange}
        h2{color:#333;margin:10px 0;font-size:1.5rem}
        .table-container{margin-bottom:15px}
        #singleTable{<?php echo $singleTableStyle; ?>}
        #groupTable{<?php echo $groupTableStyle; ?>}
        #volunteersTable{<?php echo $volunteersTableStyle; ?>}
        .header{background-color:#484622;color:white;padding:10px 0;margin-bottom:15px}
        .header h1{margin:0;font-size:1.8rem}
        .id-col{width:40px}
        .name-col{width:120px}
        .reg-col{width:100px}
        .email-col{width:160px}
        .phone-col{width:100px}
        .dept-col{width:100px}
        .section-col{width:60px}
        .event-col{width:100px}
        .category-col{width:120px}
        .game-col{width:120px}
        .members-col{width:200px}
        .app-number-col{width:100px}
        .event-id-col{width:60px}
        .event-name-col{width:120px}
        .amount-col{width:80px}
        .payment-col{width:100px}
        .date-col{width:100px}
        .branch-col{width:100px}
        .year-col{width:60px}
        .gender-col{width:60px}
        .residence-col{width:100px}
        .committee-col{width:120px}
        .filter-container{margin:15px 0;display:flex;justify-content:center;align-items:center;flex-wrap:wrap}
        .filter-container .filter-group{margin:5px 10px;display:flex;align-items:center}
        .filter-container select, .filter-container input[type="text"]{padding:8px;margin-left:5px;border-radius:4px;border:1px solid #ddd;min-width:150px}
        .filter-container button{padding:8px 16px;margin-left:10px;background:#484622;color:white;border:none;border-radius:5px;cursor:pointer}
        .filter-container button:hover{background:#5d5b2d}
        .reset-btn{padding:8px 16px;margin-left:10px;background:#666;color:white;border:none;border-radius:5px;cursor:pointer;text-decoration:none}
        .reset-btn:hover{background:#777}
        .count{font-size:0.9rem;margin-bottom:10px;color:#555}
        .search-highlight{background-color:#ffed83;font-weight:bold}
    </style>
</head>
<body>
    <div class="header">
        <h1>Sports Portal Admin</h1>
    </div>

    <div class="container">
        <h2>Student Registrations</h2>
        
        <div class="btn-container">
            <a href="viewreg.php?view=single<?php echo !empty($eventFilter) ? '&event=' . urlencode($eventFilter) : ''; ?><?php echo !empty($gameFilter) ? '&game=' . urlencode($gameFilter) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="btn">Single Player</a>
            <a href="viewreg.php?view=group<?php echo !empty($eventFilter) ? '&event=' . urlencode($eventFilter) : ''; ?><?php echo !empty($gameFilter) ? '&game=' . urlencode($gameFilter) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="btn">Group Player</a>
            <a href="viewreg.php?view=volunteers<?php echo !empty($eventFilter) ? '&event=' . urlencode($eventFilter) : ''; ?><?php echo !empty($committeeFilter) ? '&committee=' . urlencode($committeeFilter) : ''; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="btn">Volunteers</a>
        </div>

        <div id="singleTable" class="table-container" style="<?php echo $singleTableStyle; ?>">
            <h2>Single Player Registrations</h2>
            
            <form class="filter-container" method="GET" action="">
                <input type="hidden" name="view" value="single">
                
                <div class="filter-group">
                    <label for="event-filter">Event:</label>
                    <select name="event" id="event-filter">
                        <option value="">All Events</option>
                        <?php 
                        if ($result_single_events && $result_single_events->num_rows > 0) {
                            while ($row = $result_single_events->fetch_assoc()) {
                                $selected = ($eventFilter == $row['event']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['event']) . '" ' . $selected . '>' . htmlspecialchars($row['event']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="game-filter">Game:</label>
                    <select name="game" id="game-filter">
                        <option value="">All Games</option>
                        <?php 
                        if ($result_single_games && $result_single_games->num_rows > 0) {
                            $result_single_games->data_seek(0); // Reset pointer to beginning
                            while ($row = $result_single_games->fetch_assoc()) {
                                $selected = ($gameFilter == $row['game']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['game']) . '" ' . $selected . '>' . htmlspecialchars($row['game']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" placeholder="Name, Reg, Email, Phone, Dept" value="<?php echo htmlspecialchars($searchTerm); ?>">
                </div>
                
                <button type="submit">Apply Filters</button>
                
                <?php if (!empty($eventFilter) || !empty($gameFilter) || !empty($searchTerm)): ?>
                <a href="viewreg.php?view=single" class="reset-btn">Reset Filters</a>
                <?php endif; ?>
            </form>
            
            <?php if ($result_single && $result_single->num_rows > 0): ?>
                <div class="count">Showing <?php echo $result_single->num_rows; ?> registration(s)</div>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th class="id-col">ID</th>
                            <th class="name-col">Name</th>
                            <th class="reg-col">Reg Number</th>
                            <th class="email-col">Email</th>
                            <th class="phone-col">Phone</th>
                            <th class="dept-col">Department</th>
                            <th class="section-col">Section</th>
                            <th class="event-col">Event</th>
                            <th class="category-col">Event Category</th>
                            <th class="category-col">Game Category</th>
                            <th class="game-col">Game</th>
                            <th class="amount-col">Amount</th>
                            <th class="payment-col">Payment Status</th>
                            <th class="date-col">Registration Date</th>
                            <th class="payment-col">Transaction ID</th>
                            <th class="date-col">Payment Date</th>
                        </tr>
                        <?php while ($row = $result_single->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['name']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['reg_number']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['email']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['phone']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['department']), $searchTerm); ?></td>
                                <td><?php echo htmlspecialchars($row['section']); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['event']), $eventFilter); ?></td>
                                <td><?php echo htmlspecialchars($row['event_category']); ?></td>
                                <td><?php echo htmlspecialchars($row['game_category']); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['game']), $gameFilter); ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                                <td><?php echo htmlspecialchars($row['registration_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['transaction_id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_date'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php else: ?>
                <p>No single player registrations found.</p>
            <?php endif; ?>
        </div>
        
        <div id="groupTable" class="table-container" style="<?php echo $groupTableStyle; ?>">
            <h2>Group Player Registrations</h2>
            
            <form class="filter-container" method="GET" action="">
                <input type="hidden" name="view" value="group">
                
                <div class="filter-group">
                    <label for="event-filter">Event:</label>
                    <select name="event" id="event-filter">
                        <option value="">All Events</option>
                        <?php 
                        if ($result_group_events && $result_group_events->num_rows > 0) {
                            while ($row = $result_group_events->fetch_assoc()) {
                                $selected = ($eventFilter == $row['event']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['event']) . '" ' . $selected . '>' . htmlspecialchars($row['event']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="game-filter">Game:</label>
                    <select name="game" id="game-filter">
                        <option value="">All Games</option>
                        <?php 
                        if ($result_group_games && $result_group_games->num_rows > 0) {
                            while ($row = $result_group_games->fetch_assoc()) {
                                $selected = ($gameFilter == $row['game']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['game']) . '" ' . $selected . '>' . htmlspecialchars($row['game']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" placeholder="Name, Reg, Email, Phone, Dept" value="<?php echo htmlspecialchars($searchTerm); ?>">
                </div>
                
                <button type="submit">Apply Filters</button>
                
                <?php if (!empty($eventFilter) || !empty($gameFilter) || !empty($searchTerm)): ?>
                <a href="viewreg.php?view=group" class="reset-btn">Reset Filters</a>
                <?php endif; ?>
            </form>
            
            <?php if ($result_group && $result_group->num_rows > 0): ?>
                <div class="count">Showing <?php echo $result_group->num_rows; ?> registration(s)</div>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th class="id-col">ID</th>
                            <th class="name-col">Team Leader</th>
                            <th class="reg-col">Reg Number</th>
                            <th class="email-col">Email</th>
                            <th class="phone-col">Phone</th>
                            <th class="dept-col">Department</th>
                            <th class="section-col">Section</th>
                            <th class="event-col">Event</th>
                            <th class="category-col">Event Category</th>
                            <th class="category-col">Game Category</th>
                            <th class="game-col">Game</th>
                            <th class="members-col">Team Members</th>
                            <th class="amount-col">Amount</th>
                            <th class="payment-col">Payment Status</th>
                            <th class="date-col">Registration Date</th>
                            <th class="payment-col">Transaction ID</th>
                            <th class="date-col">Payment Date</th>
                        </tr>
                        <?php while ($row = $result_group->fetch_assoc()): 
                            $leader_id = $row['id'];
                            
                            $team_sql = "SELECT name, reg_number, team_member_name, team_member_reg_number FROM groupplayers WHERE group_id = $leader_id";
                            $team_result = $conn->query($team_sql);
                            $team_members = "";
                            
                            if ($team_result && $team_result->num_rows > 0) {
                                while ($member = $team_result->fetch_assoc()) {
                                    if (!empty($member['team_member_name']) && !empty($member['team_member_reg_number'])) {
                                        $team_members .= highlightSearchTerm(htmlspecialchars($member['team_member_name']), $searchTerm) . " (" . 
                                                        highlightSearchTerm(htmlspecialchars($member['team_member_reg_number']), $searchTerm) . ")<br>";
                                    } else {
                                        $team_members .= highlightSearchTerm(htmlspecialchars($member['name']), $searchTerm) . " (" . 
                                                        highlightSearchTerm(htmlspecialchars($member['reg_number']), $searchTerm) . ")<br>";
                                    }
                                }
                            } else {
                                if (!empty($row['team_member_name']) && !empty($row['team_member_reg_number'])) {
                                    $team_members = highlightSearchTerm(htmlspecialchars($row['team_member_name']), $searchTerm) . " (" . 
                                                    highlightSearchTerm(htmlspecialchars($row['team_member_reg_number']), $searchTerm) . ")";
                                } else {
                                    $team_members = "No team members added.";
                                }
                            }
                            
                            $payment_status = "Pending";
                            if (!empty($row['transaction_id']) && !empty($row['payment_date'])) {
                                $payment_status = "Completed";
                            }
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['name']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['reg_number']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['email']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['phone']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['department']), $searchTerm); ?></td>
                                <td><?php echo htmlspecialchars($row['section']); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['event']), $eventFilter); ?></td>
                                <td><?php echo htmlspecialchars($row['event_category']); ?></td>
                                <td><?php echo htmlspecialchars($row['game_category']); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['game']), $gameFilter); ?></td>
                                <td><?php echo $team_members; ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo $payment_status; ?></td>
                                <td><?php echo htmlspecialchars($row['registration_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php else: ?>
                <p>No group player registrations found.</p>
            <?php endif; ?>
        </div>
        
        <div id="volunteersTable" class="table-container" style="<?php echo $volunteersTableStyle; ?>">
            <h2>Volunteers</h2>
            
            <form class="filter-container" method="GET" action="">
                <input type="hidden" name="view" value="volunteers">
                
                <div class="filter-group">
                    <label for="event-filter">Event:</label>
                    <select name="event" id="event-filter">
                        <option value="">All Events</option>
                        <?php 
                        if ($result_volunteer_events && $result_volunteer_events->num_rows > 0) {
                            while ($row = $result_volunteer_events->fetch_assoc()) {
                                $selected = ($eventFilter == $row['event_name']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['event_name']) . '" ' . $selected . '>' . htmlspecialchars($row['event_name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="committee-filter">Committee:</label>
                    <select name="committee" id="committee-filter">
                        <option value="">All Committees</option>
                        <?php 
                        if ($result_volunteer_committees && $result_volunteer_committees->num_rows > 0) {
                            while ($row = $result_volunteer_committees->fetch_assoc()) {
                                $selected = ($committeeFilter == $row['committee']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['committee']) . '" ' . $selected . '>' . 
                                    htmlspecialchars($row['committee']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" placeholder="Name, Reg, Email, Phone, Branch, Committee" value="<?php echo htmlspecialchars($searchTerm); ?>">
                </div>
                <button type="submit">Apply Filters</button>
                <?php if (!empty($eventFilter) || !empty($committeeFilter) || !empty($searchTerm)): ?>
                <a href="viewreg.php?view=volunteers" class="reset-btn">Reset Filters</a>
                <?php endif; ?>
            </form>
            <?php if ($result_volunteers && $result_volunteers->num_rows > 0): ?>
                <div class="count">Showing <?php echo $result_volunteers->num_rows; ?> volunteer(s)</div>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th class="id-col">ID</th>
                            <th class="name-col">Name</th>
                            <th class="email-col">Email</th>
                            <th class="phone-col">Phone</th>
                            <th class="app-number-col">Reg Number</th>
                            <th class="branch-col">Branch</th>
                            <th class="year-col">Year</th>
                            <th class="residence-col">Residence</th>
                            <th class="committee-col">Committee</th>
                            <th class="event-name-col">Event Name</th>
                        </tr>
                        <?php while ($row = $result_volunteers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['volunteer_id']); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['name']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['email']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['phone']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['reg_number']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['branch']), $searchTerm); ?></td>
                                <td><?php echo htmlspecialchars($row['year']); ?></td>
                                <td><?php echo htmlspecialchars($row['residence']); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['committee']), $searchTerm); ?></td>
                                <td><?php echo highlightSearchTerm(htmlspecialchars($row['event_name']), $eventFilter); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php else: ?>
                <p>No volunteers found.</p>
            <?php endif; ?>
        </div>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>