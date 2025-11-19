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

function generateVerificationCode($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

if (isset($_POST['generate_code'])) {
    $event_id = $_POST['event_id'];
    $category = $_POST['category'];
    $verification_code = generateVerificationCode();
    
    $sql = "UPDATE attendance_verification SET is_active = 0 
            WHERE event_id = ? AND category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $event_id, $category);
    $stmt->execute();
    
    $sql = "INSERT INTO attendance_verification (event_id, category, verification_code) 
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $event_id, $category, $verification_code);
    
    if ($stmt->execute()) {
        $success_message = "Verification code generated successfully: " . $verification_code;
    } else {
        $error_message = "Error generating verification code: " . $conn->error;
    }
}

if (isset($_POST['mark_attendance'])) {
    $event_id = $_POST['event_id'];
    $category = $_POST['category'];
    $verification_code = $_POST['verification_code'];
    $selected_persons = isset($_POST['selected_persons']) ? $_POST['selected_persons'] : [];
    
    if (!empty($selected_persons)) {
        foreach ($selected_persons as $person) {
            list($person_id, $person_type, $reg_number) = explode('_', $person);
            
            $check_sql = "SELECT id FROM attendance_records 
                         WHERE person_id = ? AND person_type = ? AND event_id = ? AND verification_code = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("isis", $person_id, $person_type, $event_id, $verification_code);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                $sql = "INSERT INTO attendance_records (person_id, reg_number, person_type, event_id, verification_code) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issis", $person_id, $reg_number, $person_type, $event_id, $verification_code);
                $stmt->execute();
            }
        }
        $success_message = "Attendance marked successfully!";
    } else {
        $error_message = "No persons selected for attendance marking.";
    }
}

$events_query = "SELECT id, name FROM event ORDER BY name";
$events_result = $conn->query($events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Attendance Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.container{max-width:1200px;margin:20px auto}.card{margin-bottom:20px}.verification-code{font-size:24px;font-weight:bold;color:#28a745;padding:10px;background-color:#f8f9fa;border-radius:5px;display:inline-block;margin:10px 0}.table-responsive{max-height:500px;overflow-y:auto}.badge{font-size:0.85em;margin-right:3px}</style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Event Attendance Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>Select Event and Category</h5>
            </div>
            <div class="card-body">
                <form method="get" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="event_id" class="form-label">Select Event:</label>
                            <select class="form-select" name="event_id" id="event_id" required>
                                <option value="">-- Select Event --</option>
                                <?php while ($event = $events_result->fetch_assoc()): ?>
                                    <option value="<?php echo $event['id']; ?>" <?php echo (isset($_GET['event_id']) && $_GET['event_id'] == $event['id']) ? 'selected' : ''; ?>>
                                        <?php echo $event['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Select Category:</label>
                            <select class="form-select" name="category" id="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="singleplayer" <?php echo (isset($_GET['category']) && $_GET['category'] == 'singleplayer') ? 'selected' : ''; ?>>Single Players</option>
                                <option value="groupplayer" <?php echo (isset($_GET['category']) && $_GET['category'] == 'groupplayer') ? 'selected' : ''; ?>>Group Players</option>
                                <option value="volunteer" <?php echo (isset($_GET['category']) && $_GET['category'] == 'volunteer') ? 'selected' : ''; ?>>Volunteers</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">View Records</button>
                </form>
            </div>
        </div>
        
        <?php
        if (isset($_GET['event_id']) && isset($_GET['category'])) {
            $event_id = $_GET['event_id'];
            $category = $_GET['category'];
            
            $event_query = "SELECT name FROM event WHERE id = ?";
            $stmt = $conn->prepare($event_query);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $event_result = $stmt->get_result();
            $event_row = $event_result->fetch_assoc();
            $event_name = $event_row ? $event_row['name'] : 'Unknown Event';
            
            $code_query = "SELECT verification_code FROM attendance_verification 
                          WHERE event_id = ? AND category = ? AND is_active = 1 
                          ORDER BY generated_at DESC LIMIT 1";
            $stmt = $conn->prepare($code_query);
            $stmt->bind_param("is", $event_id, $category);
            $stmt->execute();
            $code_result = $stmt->get_result();
            $active_code = $code_result->num_rows > 0 ? $code_result->fetch_assoc()['verification_code'] : null;
            
            if ($category == 'singleplayer') {
                $players_query = "SELECT id, name, reg_number, email, phone, department, section, game_category, game, event_category 
                                 FROM singleplayer WHERE event = ? ORDER BY name";
                $stmt = $conn->prepare($players_query);
                $stmt->bind_param("s", $event_name);
                $stmt->execute();
                $players_result = $stmt->get_result();
                
                $has_players = ($players_result->num_rows > 0);
                
                if ($has_players) {
                    ?>
                    <form method="post" action="">
                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                        <input type="hidden" name="category" value="<?php echo $category; ?>">
                        
                        <div class="card mt-4">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5>Single Player Records for <?php echo $event_name; ?></h5>
                                <?php if ($active_code): ?>
                                    <div class="verification-code">Code: <?php echo $active_code; ?></div>
                                    <input type="hidden" name="verification_code" value="<?php echo $active_code; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!$active_code): ?>
                                    <button type="submit" name="generate_code" class="btn btn-warning mb-3">Generate Verification Code</button>
                                <?php else: ?>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll">
                                            <strong>Select All Persons</strong>
                                        </label>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Name</th>
                                                    <th>Reg Number</th>
                                                    <th>Department</th>
                                                    <th>Game</th>
                                                    <th>Category</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($player = $players_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>
                                                            <input class="form-check-input person-checkbox" type="checkbox" 
                                                                   name="selected_persons[]" value="<?php echo $player['id'] . '_singleplayer_' . $player['reg_number']; ?>">
                                                        </td>
                                                        <td><?php echo $player['name']; ?></td>
                                                        <td><?php echo $player['reg_number']; ?></td>
                                                        <td><?php echo $player['department'] . ' (' . $player['section'] . ')'; ?></td>
                                                        <td><?php echo $player['game']; ?></td>
                                                        <td><?php echo $player['event_category']; ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <button type="submit" name="mark_attendance" class="btn btn-success mt-3">Mark Attendance</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    <?php
                } else {
                    echo '<div class="alert alert-info mt-4">No single players found for this event.</div>';
                }
            } elseif ($category == 'groupplayer') {
                $groups_query = "SELECT id, name, reg_number, email, phone, department, section, event_category, game_category, game, 
                                team_member_name, team_member_reg_number 
                                FROM groupplayers WHERE event = ? ORDER BY name";
                $stmt = $conn->prepare($groups_query);
                $stmt->bind_param("s", $event_name);
                $stmt->execute();
                $groups_result = $stmt->get_result();
                
                if ($groups_result->num_rows > 0) {
                    ?>
                    <form method="post" action="">
                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                        <input type="hidden" name="category" value="<?php echo $category; ?>">
                        
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5>Group Player Records for <?php echo $event_name; ?></h5>
                                <?php if ($active_code): ?>
                                    <div class="verification-code">Code: <?php echo $active_code; ?></div>
                                    <input type="hidden" name="verification_code" value="<?php echo $active_code; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!$active_code): ?>
                                    <button type="submit" name="generate_code" class="btn btn-warning mb-3">Generate Verification Code</button>
                                <?php else: ?>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll">
                                            <strong>Select All Persons</strong>
                                        </label>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Team Name</th>
                                                    <th>Leader</th>
                                                    <th>Team Members</th>
                                                    <th>Game</th>
                                                    <th>Category</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($group = $groups_result->fetch_assoc()): 
                                                    $team_members = explode(',', $group['team_member_name']);
                                                    $team_reg_numbers = explode(',', $group['team_member_reg_number']);
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <input class="form-check-input person-checkbox" type="checkbox" 
                                                                   name="selected_persons[]" value="<?php echo $group['id'] . '_groupplayer_' . $group['reg_number']; ?>">
                                                        </td>
                                                        <td><?php echo $group['name']; ?></td>
                                                        <td>
                                                            <?php echo $group['reg_number']; ?>
                                                            <br><small class="text-muted"><?php echo $group['department'] . ' (' . $group['section'] . ')'; ?></small>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            for ($i = 0; $i < count($team_members); $i++) {
                                                                if (isset($team_members[$i]) && isset($team_reg_numbers[$i])) {
                                                                    echo '<span class="badge bg-info text-dark">' . $team_members[$i] . ' (' . $team_reg_numbers[$i] . ')</span> ';
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $group['game']; ?></td>
                                                        <td><?php echo $group['event_category']; ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <button type="submit" name="mark_attendance" class="btn btn-success mt-3">Mark Attendance</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    <?php
                } else {
                    echo '<div class="alert alert-info mt-4">No group players found for this event.</div>';
                }
            } elseif ($category == 'volunteer') {
                $volunteers_query = "SELECT volunteer_id, name, email, phone, reg_number, branch, year, gender, residence, committee 
                                  FROM volunteers WHERE event_name = ? ORDER BY name";
                $stmt = $conn->prepare($volunteers_query);
                $stmt->bind_param("s", $event_name);
                $stmt->execute();
                $volunteers_result = $stmt->get_result();
                
                if ($volunteers_result->num_rows > 0) {
                    ?>
                    <form method="post" action="">
                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                        <input type="hidden" name="category" value="<?php echo $category; ?>">
                        
                        <div class="card mt-4">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h5>Volunteer Records for <?php echo $event_name; ?></h5>
                                <?php if ($active_code): ?>
                                    <div class="verification-code">Code: <?php echo $active_code; ?></div>
                                    <input type="hidden" name="verification_code" value="<?php echo $active_code; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!$active_code): ?>
                                    <button type="submit" name="generate_code" class="btn btn-warning mb-3">Generate Verification Code</button>
                                <?php else: ?>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll">
                                            <strong>Select All Persons</strong>
                                        </label>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Name</th>
                                                    <th>Reg Number</th>
                                                    <th>Branch/Year</th>
                                                    <th>Committee</th>
                                                    <th>Contact</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($volunteer = $volunteers_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>
                                                            <input class="form-check-input person-checkbox" type="checkbox" 
                                                                   name="selected_persons[]" value="<?php echo $volunteer['volunteer_id'] . '_volunteer_' . $volunteer['reg_number']; ?>">
                                                        </td>
                                                        <td><?php echo $volunteer['name']; ?></td>
                                                        <td><?php echo $volunteer['reg_number']; ?></td>
                                                        <td><?php echo $volunteer['branch'] . ' (' . $volunteer['year'] . ' Year)'; ?></td>
                                                        <td><?php echo $volunteer['committee']; ?></td>
                                                        <td><?php echo $volunteer['phone']; ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <button type="submit" name="mark_attendance" class="btn btn-success mt-3">Mark Attendance</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    <?php
                } else {
                    echo '<div class="alert alert-info mt-4">No volunteers found for this event.</div>';
                }
            } 
        }
        ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.person-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                });
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>