<?php
session_start(); 
include("connection.php");
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: registration.php");
    exit();
}
if (!isset($_SESSION['reg_number'])) {
    header("Location: registration.php");
    exit();
}
$reg_number = $_SESSION['reg_number'];
$password_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $password_message = "All fields are required";
    } elseif ($new_password !== $confirm_password) {
        $password_message = "New passwords do not match";
    } else {
        $query = "SELECT password FROM registration WHERE reg_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $reg_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($current_password, $row['password']) || $current_password === $row['password']) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE registration SET password = ? WHERE reg_number = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ss", $hashed_password, $reg_number);
                if ($update_stmt->execute()) {
                    $password_message = "success";
                } else {
                    $password_message = "Failed to update password. Please try again.";
                }
            } else {
                $password_message = "Current password is incorrect";
            }
        } else {
            $password_message = "User not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Portal Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body{font-family:Arial,sans-serif;margin:0;padding:0;text-align:center;background-color:#f4f4f9;min-height:100vh;display:flex;flex-direction:column}header,footer{background-color:#484622;color:white;padding:15px 0;position:relative}header{display:flex;justify-content:space-between;align-items:center;padding:15px 30px}.header-title{font-size:24px;margin:0}.container{display:flex;justify-content:space-around;align-items:flex-start;padding:40px;margin-top:20px;flex-grow:1}.section{width:45%;text-align:left;background-color:#fff;padding:30px;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1)}.section h2{color:#000000}.button{display:inline-block;margin:10px 0;padding:15px 25px;font-size:16px;color:white;background-color:#484622;border:none;border-radius:5px;cursor:pointer;text-decoration:none}.button:hover{background-color:#5a582d}.description{font-size:18px;color:#333;line-height:1.8}.form-container{display:none;max-width:500px;margin:20px auto;padding:30px;border:1px solid #e0e0e0;border-radius:10px;background-color:#ffffff;box-shadow:0 5px 15px rgba(0,0,0,0.1)}.form-container h2{text-align:center;margin-bottom:25px;color:#333;font-size:24px}.form-container input,.form-container select,.form-container button{width:100%;margin-bottom:20px;padding:12px;border:1px solid #ddd;border-radius:5px;box-sizing:border-box;font-size:16px}.form-container input:focus,.form-container select:focus{border-color:#4a90e2;outline:none;box-shadow:0 0 3px rgba(74,144,226,0.3)}.form-container select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}.form-container button{background-color:#4a90e2;color:#fff;cursor:pointer;font-weight:bold;transition:background-color 0.3s}.form-container button:hover{background-color:#357abd}.submit-button{background-color:#0066cc!important;color:white!important}.submit-button:hover{background-color:#0055aa!important}.back-button{background-color:#e74c3c!important;color:white!important}.back-button:hover{background-color:#c0392b!important}#eventOptions select,#games select{width:100%;margin-bottom:20px;padding:12px;border:1px solid #ddd;border-radius:5px;box-sizing:border-box;font-size:16px;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}.team-member{width:100%;background-color:#f9f9f9;border-radius:5px}.team-member input{width:100%;box-sizing:border-box}.user-profile{position:relative;display:inline-block}.profile-icon{font-size:24px;color:white;cursor:pointer;width:40px;height:40px;background-color:#5a582d;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background-color 0.3s}.profile-icon:hover{background-color:#706e3c}.dropdown-menu{position:absolute;top:50px;right:0;background-color:white;width:200px;box-shadow:0 8px 16px rgba(0,0,0,0.2);border-radius:5px;z-index:100;display:none;overflow:hidden}.dropdown-menu.show{display:block}.dropdown-menu a,.dropdown-menu button{color:#333;padding:12px 16px;text-decoration:none;display:block;text-align:left;border:none;background:none;width:100%;font-size:16px;cursor:pointer;border-bottom:1px solid #f0f0f0;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.dropdown-menu a:hover,.dropdown-menu button:hover{background-color:#f5f5f5}.dropdown-menu .user-info{padding:16px;border-bottom:1px solid #e0e0e0;text-align:center;box-sizing:border-box;width:100%}.dropdown-menu .user-info .user-name{font-weight:bold;font-size:18px;color:#333;word-break:break-word}.dropdown-menu .user-info .user-email{font-size:14px;color:#777;margin-top:5px}.logout-btn{color:#d9534f!important;border-bottom:none!important}#settingsForm{display:none;width:450px;height:auto;min-height:450px;margin:20px auto;padding:30px;border:1px solid #e0e0e0;border-radius:10px;background-color:#ffffff;box-shadow:0 5px 15px rgba(0,0,0,0.1);box-sizing:border-box;overflow-y:auto}.tabs{display:flex;margin-bottom:20px;border-bottom:1px solid #ddd}.tab{padding:12px 24px;cursor:pointer;background-color:#f5f5f5;border:1px solid #ddd;border-bottom:none;border-radius:5px 5px 0 0;margin-right:5px;font-weight:500;transition:all 0.3s ease}.tab.active{background-color:#fff;border-bottom:1px solid #fff;margin-bottom:-1px;color:#0066cc}.tab-content{display:none}.tab-content.active{display:block}.form-group{margin-bottom:20px}.form-group label{display:block;margin-bottom:8px;text-align:left;font-weight:bold;color:#333}.form-group input{width:100%;box-sizing:border-box;padding:12px;border:1px solid #ddd;border-radius:5px}.button-container{display:flex;justify-content:space-between;gap:15px;margin-top:5px;width:100%}.button-container button{flex:1;margin:0;padding:12px;font-weight:bold;font-size:16px}.social-media{margin-top:15px;display:flex;justify-content:center;gap:15px}.social-media a{color:#fff;font-size:1.5rem;transition:color 0.3s}.social-media a:hover{color:#d1a74a}.alert{padding:15px;margin-bottom:20px;border-radius:5px;}.alert-success{background-color:#d4edda;color:#155724;border:1px solid #c3e6cb;}.alert-danger{background-color:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    </style>
</head>
<body>
    <header>
        <h1 class="header-title">Sports Portal</h1>
        <div class="user-profile">
            <div class="profile-icon" id="profileIcon"><i class="fas fa-user"></i></div>
            <div class="dropdown-menu" id="profileDropdown">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($reg_number); ?></div>
                </div>
                <a href="my-registrations.php"><i class="fas fa-clipboard-list"></i> My Registrations</a>
                <a href="attendance.php"><i class="fas fa-calendar-check"></i> OD</a>
                <a href="volunteers.php"><i class="fas fa-hands-helping"></i> Volunteers</a>
                <a href="tournaments.php"><i class="fa-solid fa-trophy"></i> Tournaments</a>
                <a href="#" onclick="showSettingsForm()"><i class="fas fa-cog"></i> Settings</a>
                <button type="button" class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </header>
    <div id="mainContainer" class="container">
        <div class="section">
            <h2>Single Player Game</h2>
            <p class="description">If you enjoy testing your skills and competing against yourself or others, our single-player games are perfect for you. These games not only improve your focus and self-discipline but also help you push your boundaries and achieve personal growth. Join now and take the first step towards excellence in individual sports!</p>
            <a href="#" class="button" onclick="showForm('singlePlayerForm')">Register for Single Player</a>
        </div>
        <div class="section">
            <h2>Group Game</h2>
            <p class="description">For those who thrive on teamwork and collaboration, group games offer the perfect opportunity to build bonds, strategize, and achieve together. By joining a team sport, you'll enhance your communication and leadership skills while experiencing the thrill of collective success. Sign up now and be a part of something bigger!</p>
            <a href="#" class="button" onclick="showForm('groupGameForm')">Register for Group Game</a>
        </div>
    </div>
    <!-- Single Player Registration Form -->
    <div id="singlePlayerForm" class="form-container">
        <h2>Single Player Registration</h2>
        <form action="single.php" method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="reg_number" value="<?= htmlspecialchars($reg_number) ?>" readonly>
            <input type="email" name="email" placeholder="Email" required>
            <input type="number" name="phone" placeholder="Phone Number" required>
            <select name="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">Mechanical</option>
                <option value="BBA">BBA</option>
                <option value="MBA">MBA</option>
                <option value="Psychology">Psychology</option>
                <option value="Bsc">Bsc</option>
                <option value="Msc">Msc</option>
                <option value="CIVIL">Civil</option>
            </select>
            <input type="text" name="section" placeholder="Section" required>
            <select name="event" id="event" onchange="showEventOptions()" required>
                <option value="" disabled selected>Select Event</option>
                <option value="Udgam">Udgam</option>
                <option value="ISC">Interschool Championship (ISC)</option>
                <option value="IHC">Interhostel Championship (IHC)</option>
                <option value="NSD">National Sports Day</option>
                <option value="YogaDay">Yoga Day</option>
                <option value="PhDMeet">PhD Scholars Meet</option>
            </select>
            <div id="eventOptions"></div>
            <select name="game_category" id="gameCategory" onchange="showGames()" required>
                <option value="" disabled selected>Select Game Category</option>
                <option value="Indoor">Indoor</option>
                <option value="RacquetGames">Racquet Games</option>
                <option value="Athletics">Athletics</option>
                <option value="Yoga">Yoga and Meditation</option>
                <option value="Gym">Gym</option>
            </select>
            <div id="games"></div>
            <div class="button-container">
                <button type="submit" class="submit-button">Submit</button>
                <button type="button" class="back-button" onclick="goBack()">Back</button>
            </div>
        </form>
    </div>
    <!-- Group Game Registration Form -->
    <div id="groupGameForm" class="form-container">
        <h2>Group Game Registration</h2>
        <form action="group.php" method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="reg_number" value="<?= htmlspecialchars($reg_number) ?>" readonly>
            <input type="email" name="email" placeholder="Email" required>
            <input type="number" name="phone" placeholder="Phone Number" required>
            <select name="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">Mechanical</option>
                <option value="CIVIL">Civil</option>
                <option value="BBA">BBA</option>
                <option value="MBA">MBA</option>
                <option value="Psychology">Psychology</option>
                <option value="Bsc">Bsc</option>
                <option value="Msc">Msc</option>
            </select>
            <input type="text" name="section" placeholder="Section" required>
            <select name="event" id="eventGroup" onchange="showEventOptionsGroup()" required>
                <option value="" disabled selected>Select Event</option>
                <option value="Udgam">Udgam</option>
                <option value="ISC">Interschool Championship (ISC)</option>
                <option value="IHC">Interhostel Championship (IHC)</option>
                <option value="NSD">National Sports Day</option>
                <option value="YogaDay">Yoga Day</option>
                <option value="PhDMeet">PhD Scholars Meet</option>
            </select>
            <div id="eventOptionsGroup"></div>
            <select name="game_category" id="gameCategoryGroup" onchange="showGamesGroup()" required>
                <option value="" disabled selected>Select Game Category</option>
                <option value="Indoor">Indoor</option>
                <option value="Outdoor">Outdoor</option>
                <option value="RacquetGames">Racquet Games</option>
                <option value="Athletics">Athletics</option>
                <option value="Yoga">Yoga and Meditation</option>
                <option value="Gym">Gym</option>
            </select>
            <div id="gamesGroup"></div>
            <div id="teamMembers"></div>
            <div class="button-container">
                <button type="submit" class="submit-button">Submit</button>
                <button type="button" class="back-button" onclick="goBack()">Back</button>
            </div>
        </form>
    </div>
    <!-- Settings Form with Password Change/Reset Options -->
        <div id="settingsForm" class="form-container">
        <h2>Account Settings</h2>
        <div class="tabs">
            <div class="tab active" onclick="showTab('changePasswordTab')">Change Password</div>
            <div class="tab" onclick="showTab('forgotPasswordTab')">Forgot Password</div>
        </div>
            <!-- Change Password Tab -->
            <div id="changePasswordTab" class="tab-content active">
            <?php if ($password_message === "success"): ?>
                <div class="alert alert-success">Password updated successfully!</div>
                <script>
                    setTimeout(function() {
                        window.location.href = "registration.php";
                    }, 2000);
                </script>
            <?php elseif (!empty($password_message)): ?>
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
            <!-- Forgot Password Tab -->
            <div id="forgotPasswordTab" class="tab-content">
            <p>
                If you've forgotten your password or are unable to log in, please contact the administrator for assistance in resetting your password.<br>
                You can also try recovering your account using your registered Application Number or Registration Number.<br>
                For security reasons, direct password changes are not permitted through this portal.<br>
                Kindly reach out to the official support team or designated administrator with your registered details for verification and further help.<br>
            <br>📧 For technical assistance, email us at 
                <a href="mailto:sportsevent.helpdesk@srmap.edu.in">sportsevent.helpdesk@srmap.edu.in</a>.<br>
                🕐 Response time: within 24-48 working hours.
            <div class="button-container">
                <button type="button" class="back-button" onclick="goBack()">Back</button>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 SRM University. All rights reserved.</p>
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
        
        function showForm(formId) {
            document.getElementById('mainContainer').style.display = 'none';
            document.getElementById(formId).style.display = 'block';
        }
        
        function showSettingsForm() {
            document.getElementById('mainContainer').style.display = 'none';
            document.getElementById('singlePlayerForm').style.display = 'none';
            document.getElementById('groupGameForm').style.display = 'none';
            document.getElementById('settingsForm').style.display = 'block';
            document.getElementById('profileDropdown').classList.remove('show');
        }
        
        function redirectToRegistration(event) {
            event.preventDefault(); 
            setTimeout(() => {
                alert("Password updated successfully!");
                window.location.href = "registration.php"; 
            }, 500);
        }
        
        function showTab(tabId) {
            var tabContents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            var tabs = document.getElementsByClassName('tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            document.getElementById(tabId).classList.add('active');
            var tabLinks = document.getElementsByClassName('tab');
            for (var i = 0; i < tabLinks.length; i++) {
                if (tabLinks[i].getAttribute('onclick').includes(tabId)) {
                    tabLinks[i].classList.add('active');
                }
            }
        }
        
        function goBack() {
            document.getElementById('singlePlayerForm').style.display = 'none';
            document.getElementById('groupGameForm').style.display = 'none';
            document.getElementById('settingsForm').style.display = 'none';
            document.getElementById('mainContainer').style.display = 'flex';
        }
        
        function showEventOptions() {
            const event = document.getElementById('event').value;
            const eventOptions = document.getElementById('eventOptions');
            let options = '';
            if (event === 'ISC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select ISC Category</option>
                        <option value="SEAS">SEAS</option>
                        <option value="PSB">PSB</option>
                        <option value="ESLA">ESLA</option>
                    </select>`;
            } else if (event === 'IHC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select Hostel</option>
                        <option value="Vedavathi">Vedavathi</option>
                        <option value="Ganga">Ganga</option>
                        <option value="Krishna">Krishna</option>
                        <option value="Yamuna">Yamuna</option>
                        <option value="Narmada">Narmada</option>
                        <option value="Kaveri">Kaveri</option>
                        <option value="Godavari">Godavari</option>
                    </select>`;
            }
            eventOptions.innerHTML = options;
            eventOptions.style.display = options ? 'block' : 'none';
        }
        
        function showGames() {
            const category = document.getElementById('gameCategory').value;
            const gamesDiv = document.getElementById('games');
            let options = '';
            
            if (category === 'Indoor') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Indoor Game</option>
                        <option value="Carroms">Carroms</option>
                        <option value="Chess">Chess</option>
                    </select>`;
            } else if (category === 'RacquetGames') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Racket Game</option>
                        <option value="Tennis">Tennis</option>
                        <option value="Badminton">Badminton</option>
                        <option value="Table Tennis">Table Tennis</option>
                    </select>`;
            } else if (category === 'Athletics') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Athletics Game</option>
                        <option value="Relay">Relay</option>
                    </select>`;
            } else if (category === 'Yoga') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Yoga Activity</option>
                        <option value="Yoga">Yoga</option>
                        <option value="Meditation">Meditation</option>
                    </select>`;
            } else if (category === 'Gym') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Gym Activity</option>
                        <option value="Gym">Gym</option>
                    </select>`;
            }
            
            gamesDiv.innerHTML = options;
            gamesDiv.style.display = options ? 'block' : 'none';
        }
        
        function showEventOptionsGroup() {
            const event = document.getElementById('eventGroup').value;
            const eventOptionsGroup = document.getElementById('eventOptionsGroup');
            let options = '';
            if (event === 'ISC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select ISC Category</option>
                        <option value="SEAS">SEAS</option>
                        <option value="PSB">PSB</option>
                        <option value="ESLA">ESLA</option>
                    </select>`;
            } else if (event === 'IHC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select Hostel</option>
                        <option value="Vedavathi">Vedavathi</option>
                        <option value="Ganga">Ganga</option>
                        <option value="Krishna">Krishna</option>
                        <option value="Yamuna">Yamuna</option>
                        <option value="Narmada">Narmada</option>
                        <option value="Kaveri">Kaveri</option>
                        <option value="Godavari">Godavari</option>
                    </select>`;
            }
            eventOptionsGroup.innerHTML = options;
            eventOptionsGroup.style.display = options ? 'block' : 'none';
        }
        
        function showGamesGroup() {
            const category = document.getElementById('gameCategoryGroup').value;
            const gamesGroup = document.getElementById('gamesGroup');
            const teamMembers = document.getElementById('teamMembers');
            let options = '';
            if (category === 'Indoor') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Indoor Game</option>
                        <option value="Carroms">Carroms</option>
                        <option value="Table Tennis">Table Tennis</option>
                    </select>`;
            } else if (category === 'Outdoor') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Outdoor Game</option>
                        <option value="Volleyball">Volleyball</option>
                        <option value="Football">Football</option>
                        <option value="Kabaddi">Kabaddi</option>
                        <option value="Kho-Kho">Kho-Kho</option>
                        <option value="Cricket">Cricket</option>
                        <option value="Basketball">Basketball</option>
                    </select>`;
            } else if (category === 'RacquetGames') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Racket Game</option>
                        <option value="Tennis">Tennis</option>
                        <option value="Badminton">Badminton</option>
                        <option value="Table Tennis">Table Tennis</option>
                    </select>`;
            } else if (category === 'Athletics') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Athletics Game</option>
                        <option value="Relay">Relay</option>
                        <option value="Tug of War">Tug of War</option>
                    </select>`;
            }
            gamesGroup.innerHTML = options;
            gamesGroup.style.display = options ? 'block' : 'none';
            teamMembers.innerHTML = '';
            teamMembers.style.display = 'none';
        }
        function showTeamMembers(game) {
            const teamMembers = document.getElementById('teamMembers');
            let memberFields = '';
            let teamSize = 2; 
            if (game === 'Cricket'|| game === 'Football' ) {
                teamSize = 11;
            } else if (game === 'Basketball' || game === 'Volleyball') {
                teamSize = 6;
            } else if (game === 'Kabaddi' || game === 'Kho-Kho') {
                teamSize = 7;
            } else if (game === 'Tennis' || game === 'Badminton' || game === 'Table Tennis') {
                teamSize = 2;
            } else if (game === 'Relay' || game === 'Tug of War') {
                teamSize = 4;
            }
            for (let i = 1; i <= teamSize; i++) {
                memberFields += `
                    <div class="team-member">
                        <input type="text" name="member_name[]" placeholder="player ${i} Name" required>
                        <input type="text" name="member_reg[]" placeholder="player ${i} Registration Number" required>
                    </div>`;
            }
            teamMembers.innerHTML = memberFields;
            teamMembers.style.display = memberFields ? 'block' : 'none';
        }
    </script>
</body>
</html>