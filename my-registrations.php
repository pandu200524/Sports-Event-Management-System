<?php
session_start();
include("connection.php");
if (!isset($_SESSION['reg_number'])) {
    header("Location: registration.php");
    exit();
}
$app_number = $_SESSION['reg_number'];
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
    return [];
}
function getSinglePlayerRegistrations($conn, $app_number) {
    $query = "SELECT * FROM singleplayer WHERE reg_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_number);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
function getGroupGameLeaderRegistrations($conn, $app_number) {
    $query = "SELECT * FROM groupplayers WHERE reg_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_number);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
function getGroupGameMemberRegistrations($conn, $app_number) {
    $query = "SELECT * FROM groupplayers WHERE FIND_IN_SET(?, team_member_reg_number) > 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $app_number);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
$userDetails = getUserDetails($conn, $app_number);
$singlePlayerRegistrations = getSinglePlayerRegistrations($conn, $app_number);
$groupGameLeaderRegistrations = getGroupGameLeaderRegistrations($conn, $app_number);
$groupGameMemberRegistrations = getGroupGameMemberRegistrations($conn, $app_number);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Registrations - Sports Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body{font-family:Arial,sans-serif;margin:0;padding:0;background-color:#f4f4f9;min-height:100vh;display:flex;flex-direction:column}header,footer{background-color:#484622;color:white;padding:15px 0;position:relative}header{display:flex;justify-content:space-between;align-items:center;padding:15px 30px}.header-title{font-size:24px;margin:0}.container{flex-grow:1;padding:30px;max-width:1200px;margin:0 auto}.card{background-color:#fff;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);margin-bottom:30px;overflow:hidden}.card-header{background-color:rgb(188,131,25);color:white;padding:15px 20px;font-size:18px;font-weight:bold}.card-body{padding:20px}.registration-item{border-bottom:1px solid #eee;padding:15px 0}.registration-item:last-child{border-bottom:none}.registration-title{font-size:18px;font-weight:bold;margin-bottom:5px;color:#333}.registration-info{display:flex;flex-wrap:wrap;margin-top:10px}.info-item{margin-right:20px;margin-bottom:10px}.info-label{font-weight:bold;color:#555}.empty-message{text-align:center;padding:20px;color:#777;font-style:italic}.button{display:inline-block;padding:10px 15px;background-color:#484622;color:white;text-decoration:none;border-radius:4px;margin-top:20px}.button:hover{background-color:#5a582d}.team-members{margin-top:15px;padding:10px;background-color:#f9f9f9;border-radius:5px}.team-members h4{margin-top:0;margin-bottom:10px;color:#333}.member-item{padding:5px 0}.user-profile{position:relative;display:inline-block}.profile-icon{font-size:24px;color:white;cursor:pointer;width:40px;height:40px;background-color:#5a582d;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background-color 0.3s}.profile-icon:hover{background-color:#706e3c}.dropdown-menu{position:absolute;top:50px;right:0;background-color:white;min-width:200px;box-shadow:0 8px 16px rgba(0,0,0,0.2);border-radius:5px;z-index:100;display:none}.dropdown-menu.show{display:block}.dropdown-menu a,.dropdown-menu button{color:#333;padding:12px 16px;text-decoration:none;display:block;text-align:left;border:none;background:none;width:100%;font-size:16px;cursor:pointer;border-bottom:1px solid #f0f0f0}.dropdown-menu a:hover,.dropdown-menu button:hover{background-color:#f5f5f5}.dropdown-menu .user-info{padding:16px;border-bottom:1px solid #e0e0e0;text-align:center}.dropdown-menu .user-info .user-name{font-weight:bold;font-size:18px;color:#333}.dropdown-menu .user-info .user-email{font-size:14px;color:#777;margin-top:5px}.logout-btn{color:#d9534f !important;border-bottom:none !important}.back-button{display:inline-block;padding:10px 15px;background-color:#6c757d;color:white;text-decoration:none;border-radius:4px;margin-top:20px}.back-button:hover{background-color:#5a6268}.payment-status{display:inline-block;padding:3px 8px;border-radius:3px;font-size:12px;font-weight:bold}.payment-status.paid{background-color:#d4edda;color:#155724}.payment-status.pending{background-color:#fff3cd;color:#856404}.status-badge{display:inline-block;padding:3px 8px;border-radius:3px;margin-left:10px;font-size:12px;font-weight:bold}.social-media{margin-top:15px;display:flex;justify-content:center;gap:15px}.social-media a{color:#fff;font-size:1.5rem;transition:color 0.3s}.social-media a:hover{color:#d1a74a}footer{background-color:#484622;color:white;text-align:center;padding:20px;margin-top:50px}.form-container{display:none;max-width:500px;margin:20px auto;padding:30px;border:1px solid #e0e0e0;border-radius:10px;background-color:#ffffff;box-shadow:0 5px 15px rgba(0,0,0,0.1)}.form-container h2{text-align:center;margin-bottom:25px;color:#333;font-size:24px}.form-container input,.form-container select,.form-container button{width:100%;margin-bottom:20px;padding:12px;border:1px solid #ddd;border-radius:5px;box-sizing:border-box;font-size:16px}.form-container input:focus,.form-container select:focus{border-color:#4a90e2;outline:none;box-shadow:0 0 3px rgba(74,144,226,0.3)}.form-container select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}.form-container button{background-color:#4a90e2;color:#fff;cursor:pointer;font-weight:bold;transition:background-color 0.3s}.form-container button:hover{background-color:#357abd}.tabs{display:flex;margin-bottom:20px;border-bottom:1px solid #ddd}#settingsForm{display:none;width:450px;height:auto;min-height:450px;margin:20px auto;padding:30px;border:1px solid #e0e0e0;border-radius:10px;background-color:#ffffff;box-shadow:0 5px 15px rgba(0,0,0,0.1);box-sizing:border-box;overflow-y:auto}.tab{padding:10px 20px;cursor:pointer;transition:background-color 0.3s}.tab.active{background-color:#484622;color:white;border-radius:4px 4px 0 0}.tab-content{display:none}.tab-content.active{display:block}.form-group{margin-bottom:20px}.form-group label{display:block;margin-bottom:5px;font-weight:bold}.form-group input{width:100%;padding:10px;border:1px solid #ddd;border-radius:4px}.button-container{display:flex;gap:10px;margin-top:30px}.submit-button, .back-button{background-color:#4a90e2;color:white;cursor:pointer;font-weight:bold;transition:background-color 0.3s;flex:1;padding:12px 15px;border:none;border-radius:5px;font-size:16px;text-align:center;text-decoration:none;display:inline-block}.submit-button{background-color:#0066cc!important;color:white!important}.submit-button:hover{background-color:#0055aa!important}.back-button{background-color:#e74c3c!important;color:white!important}.back-button:hover{background-color:#c0392b!important}.alert{padding:10px;margin-bottom:20px;border-radius:4px}.alert-success{background-color:#d4edda;color:#155724}.alert-danger{background-color:#f8d7da;color:#721c24}.user-profile{position:relative;display:inline-block}.profile-icon{position:relative;font-size:24px;color:white;cursor:pointer;width:40px;height:40px;background-color:#5a582d;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background-color 0.3s;box-sizing:content-box}.profile-icon i{display:flex;align-items:center;justify-content:center;width:100%;height:100%}
    </style>
</head>
<body>
    <header>
        <h1 class="header-title">Sports Portal</h1>
        <div class="user-profile">
            <div class="profile-icon" id="profileIcon"><i class="fas fa-user"></i></div>
            <div class="dropdown-menu" id="profileDropdown">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($app_number); ?></div>
                    <?php if(isset($userDetails['email'])): ?>
                    <div class="user-email"><?php echo htmlspecialchars($userDetails['email']); ?></div>
                    <?php endif; ?>
                </div>
                <a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
                <a href="volunteers.php"><i class="fas fa-hands-helping"></i> Volunteers</a>
                <a href="tournaments.php"><i class="fa-solid fa-trophy"></i> Tournaments</a>
                <a href="javascript:void(0);" onclick="showSettingsForm()"><i class="fas fa-cog"></i> Settings</a>
                <button type="button" class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </header>

    <div class="container" id="mainContainer">
        <h2>My Registrations</h2>
        <p>Welcome, <?php echo isset($userDetails['name']) ? htmlspecialchars($userDetails['name']) : htmlspecialchars($app_number); ?>! Here are all your sports event registrations.</p>
        
        <div class="card">
            <div class="card-header">Single Player Registrations</div>
            <div class="card-body">
                <?php if(empty($singlePlayerRegistrations)): ?>
                    <div class="empty-message">You have not registered for any single player events yet.</div>
                <?php else: ?>
                    <?php foreach($singlePlayerRegistrations as $registration): ?>
                        <div class="registration-item">
                            <div class="registration-title">
                                <?php echo htmlspecialchars($registration['game']); ?> 
                                (<?php echo htmlspecialchars($registration['event']); ?>)
                                <?php if(isset($registration['payment_status'])): ?>
                                    <span class="status-badge <?php echo strtolower($registration['payment_status']) === 'paid' ? 'paid' : 'pending'; ?>">
                                        <?php echo htmlspecialchars($registration['payment_status']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="registration-info">
                                <div class="info-item">
                                    <span class="info-label">Category:</span> 
                                    <?php echo htmlspecialchars($registration['game_category']); ?>
                                </div>
                                <?php if(!empty($registration['event_category'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Event Category:</span> 
                                    <?php echo htmlspecialchars($registration['event_category']); ?>
                                </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <span class="info-label">Department:</span> 
                                    <?php echo htmlspecialchars($registration['department']); ?>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Section:</span> 
                                    <?php echo htmlspecialchars($registration['section']); ?>
                                </div>
                                <?php if(isset($registration['amount'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Amount:</span> 
                                    ₹<?php echo htmlspecialchars($registration['amount']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['registration_date'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Registration Date:</span> 
                                    <?php echo date('M d, Y', strtotime($registration['registration_date'])); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['transaction_id']) && !empty($registration['transaction_id'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Transaction ID:</span> 
                                    <?php echo htmlspecialchars($registration['transaction_id']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['payment_date']) && !empty($registration['payment_date'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Payment Date:</span> 
                                    <?php echo date('M d, Y', strtotime($registration['payment_date'])); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">Group Game Registrations (Team Leader)</div>
            <div class="card-body">
                <?php if(empty($groupGameLeaderRegistrations)): ?>
                    <div class="empty-message">You have not registered as a team leader for any group events yet.</div>
                <?php else: ?>
                    <?php foreach($groupGameLeaderRegistrations as $registration): ?>
                        <div class="registration-item">
                            <div class="registration-title">
                                <?php echo htmlspecialchars($registration['game']); ?> 
                                (<?php echo htmlspecialchars($registration['event']); ?>)
                                <?php if(isset($registration['payment_status'])): ?>
                                    <span class="status-badge <?php echo strtolower($registration['payment_status']) === 'paid' ? 'paid' : 'pending'; ?>">
                                        <?php echo htmlspecialchars($registration['payment_status']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="registration-info">
                                <div class="info-item">
                                    <span class="info-label">Category:</span> 
                                    <?php echo htmlspecialchars($registration['game_category']); ?>
                                </div>
                                <?php if(!empty($registration['event_category'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Event Category:</span> 
                                    <?php echo htmlspecialchars($registration['event_category']); ?>
                                </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <span class="info-label">Department:</span> 
                                    <?php echo htmlspecialchars($registration['department']); ?>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Section:</span> 
                                    <?php echo htmlspecialchars($registration['section']); ?>
                                </div>
                                <?php if(isset($registration['amount'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Amount:</span> 
                                    ₹<?php echo htmlspecialchars($registration['amount']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['registration_date'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Registration Date:</span> 
                                    <?php echo date('M d, Y', strtotime($registration['registration_date'])); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['transaction_id']) && !empty($registration['transaction_id'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Transaction ID:</span> 
                                    <?php echo htmlspecialchars($registration['transaction_id']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['payment_date']) && !empty($registration['payment_date'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Payment Date:</span> 
                                    <?php echo date('M d, Y', strtotime($registration['payment_date'])); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="team-members">
                                <h4>Team Members</h4>
                                <?php
                                if (!empty($registration['team_member_name']) && !empty($registration['team_member_reg_number'])) {
                                    $memberNames = explode(',', $registration['team_member_name']);
                                    $memberRegs = explode(',', $registration['team_member_reg_number']);
                                    $count = min(count($memberNames), count($memberRegs));
                                    
                                    if ($count > 0) {
                                        for ($i = 0; $i < $count; $i++) {
                                            ?>
                                            <div class="member-item">
                                                <strong><?php echo htmlspecialchars(trim($memberNames[$i])); ?></strong>
                                                (<?php echo htmlspecialchars(trim($memberRegs[$i])); ?>)
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="empty-message">No team members found.</div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="empty-message">No team members found.</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">Group Game Registrations (Team Member)</div>
            <div class="card-body">
                <?php if(empty($groupGameMemberRegistrations)): ?>
                    <div class="empty-message">You have not been added as a team member to any group events yet.</div>
                <?php else: ?>
                    <?php foreach($groupGameMemberRegistrations as $registration): ?>
                        <div class="registration-item">
                            <div class="registration-title">
                                <?php echo htmlspecialchars($registration['game']); ?> 
                                (<?php echo htmlspecialchars($registration['event']); ?>)
                                <?php if(isset($registration['payment_status'])): ?>
                                    <span class="status-badge <?php echo strtolower($registration['payment_status']) === 'paid' ? 'paid' : 'pending'; ?>">
                                        <?php echo htmlspecialchars($registration['payment_status']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="registration-info">
                                <div class="info-item">
                                    <span class="info-label">Team Leader:</span> 
                                    <?php echo htmlspecialchars($registration['name']); ?> 
                                    (<?php echo htmlspecialchars($registration['reg_number']); ?>)
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Category:</span> 
                                    <?php echo htmlspecialchars($registration['game_category']); ?>
                                </div>
                                <?php if(!empty($registration['event_category'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Event Category:</span> 
                                    <?php echo htmlspecialchars($registration['event_category']); ?>
                                </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <span class="info-label">Department:</span> 
                                    <?php echo htmlspecialchars($registration['department']); ?>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Section:</span> 
                                    <?php echo htmlspecialchars($registration['section']); ?>
                                </div>
                                <?php if(isset($registration['amount'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Amount:</span> 
                                    ₹<?php echo htmlspecialchars($registration['amount']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['registration_date'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Registration Date:</span> 
                                    <?php echo date('M d, Y', strtotime($registration['registration_date'])); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['transaction_id']) && !empty($registration['transaction_id'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Transaction ID:</span> 
                                    <?php echo htmlspecialchars($registration['transaction_id']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if(isset($registration['payment_date']) && !empty($registration['payment_date'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Payment Date:</span> 
                                    <?php echo date('M d, Y', strtotime($registration['payment_date'])); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="team-members">
                                <h4>Team Members</h4>
                                <?php
                                if (!empty($registration['team_member_name']) && !empty($registration['team_member_reg_number'])) {
                                    $memberNames = explode(',', $registration['team_member_name']);
                                    $memberRegs = explode(',', $registration['team_member_reg_number']);
                                    $count = min(count($memberNames), count($memberRegs));
                                    
                                    if ($count > 0) {
                                        for ($i = 0; $i < $count; $i++) {
                                            $currentMemberReg = trim($memberRegs[$i]);
                                            ?>
                                            <div class="member-item">
                                                <strong><?php echo htmlspecialchars(trim($memberNames[$i])); ?></strong>
                                                (<?php echo htmlspecialchars($currentMemberReg); ?>)
                                                <?php if($currentMemberReg == $app_number): ?>
                                                    <span style="color: #484622; font-weight: bold;"> (You)</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="empty-message">No team members found.</div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="empty-message">No team members found.</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <a href="open_single_group.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Sports Portal</a>
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