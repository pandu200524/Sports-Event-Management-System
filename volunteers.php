<?php
session_start();
include("connection.php");

if (!isset($_SESSION['reg_number'])) {
    header("Location: registration.php");
    exit();
}

$app_number = $_SESSION['reg_number'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;
$name = $email = $reg_number = $branch = $year = $gender = $residence = $phone = $committee = $experience = $ideas = $improvements = $event = "";
$error = "";
$success = "";
$events = [];
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$event_query = "SELECT id, name FROM event ORDER BY name";
$event_result = $conn->query($event_query);
if ($event_result) {
    while ($row = $event_result->fetch_assoc()) {
        $events[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(htmlspecialchars($_POST["name"]));
    $email = trim(filter_var($_POST["email"], FILTER_SANITIZE_EMAIL));
    $reg_number = trim(htmlspecialchars($_POST["reg_number"]));
    $branch = trim(htmlspecialchars($_POST["branch"]));
    $year = trim(htmlspecialchars($_POST["year"]));
    $gender = trim(htmlspecialchars($_POST["gender"]));
    $residence = trim(htmlspecialchars($_POST["residence"]));
    $phone = trim(htmlspecialchars($_POST["phone"]));
    $committee = trim(htmlspecialchars($_POST["committee"]));
    $experience = isset($_POST["experience"]) && !empty(trim($_POST["experience"])) ? trim(htmlspecialchars($_POST["experience"])) : NULL;
    $ideas = isset($_POST["ideas"]) && !empty(trim($_POST["ideas"])) ? trim(htmlspecialchars($_POST["ideas"])) : NULL;
    $improvements = isset($_POST["improvements"]) && !empty(trim($_POST["improvements"])) ? trim(htmlspecialchars($_POST["improvements"])) : NULL;
    
    $event = trim(htmlspecialchars($_POST["event"]));
    if (empty($name) || empty($email) || empty($reg_number) || empty($branch) || empty($year) || empty($gender) || empty($residence) || empty($phone) || empty($committee) || empty($event)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $sql = "INSERT INTO volunteers (name, email, reg_number, branch, year, gender, residence, phone, committee, experience, ideas, improvements, event_id, event_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $event_name = "";
        foreach ($events as $event_item) {
            if ($event_item['id'] == $event) {
                $event_name = $event_item['name'];
                break;
            }
        }
        $stmt->bind_param("ssssssssssssis", $name, $email, $reg_number, $branch, $year, $gender, $residence, $phone, $committee, $experience, $ideas, $improvements, $event, $event_name);
        
        if ($stmt->execute()) {
            $success = "Thank you for registering as a volunteer!";
            $name = $email = $reg_number = $branch = $year = $gender = $residence = $phone = $committee = $experience = $ideas = $improvements = $event = "";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

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

$userDetails = getUserDetails($conn, $app_number);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Volunteer Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
body{font-family:Arial,sans-serif;margin:0;padding:0;background-color:#f4f4f9;min-height:100vh;display:flex;flex-direction:column}header,footer{background-color:#484622;color:white;padding:15px 0;position:relative}.container{max-width:900px;background:white;margin:auto;padding:20px;margin-top:30px;border-radius:8px;box-shadow:0 0 15px rgba(0,0,0,0.1)}h1{text-align:center;color:rgb(188,131,25);margin-bottom:20px}h2{color:#1a73e8}.form-group{margin-bottom:15px}.horizontal-form-group{display:flex;align-items:center;margin-bottom:15px}.horizontal-form-group label{width:150px;margin-right:10px;margin-bottom:0}.horizontal-form-group input,.horizontal-form-group select{flex:1}label{font-weight:bold;display:block;margin-bottom:5px}input[type="text"],input[type="email"],input[type="tel"],input[type="password"],select,textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box}textarea{height:60px;resize:vertical}select{height:40px}.radio-group{display:flex;gap:15px;align-items:center}.radio-option{display:flex;align-items:center;gap:5px}.back-button{display:inline-block;padding:10px 15px;background-color:#6c757d;color:white;text-decoration:none;border-radius:4px;margin-top:10px}.back-button:hover{background-color:#5a6268}button{background-color:rgb(188,131,25);padding:12px 20px;color:white;border:none;cursor:pointer;font-size:16px;border-radius:4px;width:100%;transition:background-color 0.3s}button:hover{background-color:orange}.error{color:#d32f2f;margin-bottom:10px;padding:10px;background-color:#ffeaea;border-radius:4px}.success{color:#388e3c;margin-bottom:10px;padding:10px;background-color:#eaffea;border-radius:4px}.required{color:#d32f2f}.perks{background-color:#f9f9f9;padding:15px;border-radius:8px;margin:20px 0;box-shadow:0 2px 4px rgba(0,0,0,0.05)}.perks h3{color:#1a73e8;margin-top:0}.perks ul{padding-left:20px}.perks li{margin-bottom:10px}.social-media{margin-top:15px;display:flex;justify-content:center;gap:15px}.social-media a{color:#fff;font-size:1.5rem;transition:color 0.3s}.social-media a:hover{color:#d1a74a}footer{background-color:#484622;color:white;text-align:center;padding:20px;margin-top:50px}header{display:flex;justify-content:space-between;align-items:center;padding:15px 30px}.header-title{font-size:24px;margin:0;color:white;text-shadow:1px 1px 2px rgba(0,0,0,0.5)}.user-profile{position:relative;display:inline-block}.profile-icon{font-size:24px;color:white;cursor:pointer;width:40px;height:40px;background-color:#5a582d;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background-color 0.3s}.profile-icon:hover{background-color:#706e3c}.dropdown-menu{position:absolute;top:50px;right:0;background-color:white;min-width:200px;box-shadow:0 8px 16px rgba(0,0,0,0.2);border-radius:5px;z-index:100;display:none}.dropdown-menu.show{display:block}.dropdown-menu a,.dropdown-menu button{color:#333;padding:12px 16px;text-decoration:none;display:block;text-align:left;border:none;background:none;width:100%;font-size:16px;cursor:pointer;border-bottom:1px solid #f0f0f0}.dropdown-menu a:hover,.dropdown-menu button:hover{background-color:#f5f5f5}.dropdown-menu .user-info{padding:16px;border-bottom:1px solid #e0e0e0;text-align:center}.dropdown-menu .user-info .user-name{font-weight:bold;font-size:18px;color:#333}.dropdown-menu .user-info .user-email{font-size:14px;color:#777;margin-top:5px}.logout-btn{color:#d9534f !important;border-bottom:none !important}#settingsForm{display:none;width:450px;height:auto;min-height:450px;margin:20px auto;padding:30px;border:1px solid #e0e0e0;border-radius:10px;background-color:#ffffff;box-shadow:0 5px 15px rgba(0,0,0,0.1);box-sizing:border-box;overflow-y:auto;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:1000}.tab{padding:10px 20px;cursor:pointer;transition:background-color 0.3s}.tab.active{background-color:#484622;color:white;border-radius:4px 4px 0 0}.tab-content{display:none}.tab-content.active{display:block}.form-group{margin-bottom:20px}.button-container{display:flex;gap:10px;margin-top:30px}.submit-button,.back-button{background-color:#4a90e2;color:white;cursor:pointer;font-weight:bold;transition:background-color 0.3s;flex:1;padding:12px 15px;border:none;border-radius:5px;font-size:16px;text-align:center;text-decoration:none;display:inline-block}.submit-button{background-color:#0066cc!important;color:white!important}.submit-button:hover{background-color:#0055aa!important}.back-button{background-color:#e74c3c!important;color:white!important}.back-button:hover{background-color:#c0392b!important}.overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.5);z-index:900}.alert{padding:10px;margin-bottom:15px;border-radius:4px}.alert-success{color:#388e3c;background-color:#eaffea}.alert-danger{color:#d32f2f;background-color:#ffeaea}
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
                <a href="my-registrations.php"><i class="fas fa-clipboard-list"></i> My Registrations</a>
                <a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
                <a href="tournaments.php"><i class="fa-solid fa-trophy"></i> Tournaments</a>
                <a href="javascript:void(0);" onclick="showSettingsForm()"><i class="fas fa-cog"></i> Settings</a>
                <button type="button" class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </header>
    
    <div class="container" id="mainContainer">
        <h1>Volunteers Registration</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <p>SRM University-AP is looking for passionate, dedicated, and energetic volunteers to make our events a massive success!</p>
        
        <div class="perks">
            <h3>Benefits of Volunteering:</h3>
            <ul>
                <li><strong>Receive exciting goodies and appreciation certificates</strong></li>
                <li><strong>Gain exposure and networking opportunities in campus</strong></li>
                <li><strong>Enjoy on duty attendance without academic disruptions</strong></li>
                <li><strong>Build valuable work experiences in event management</strong></li>
                <li><strong>Be a part of prestigious national level events</strong></li>
            </ul>
        </div>
        
        <form method="post" action="">
            <div class="horizontal-form-group">
                <label for="event">Select Event <span class="required">*</span></label>
                <select id="event" name="event" required>
                    <option value="">-- Select an Event --</option>
                    <?php foreach ($events as $event_item): ?>
                        <option value="<?php echo $event_item['id']; ?>" <?php if ($event == $event_item['id']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($event_item['name']); ?> <?php if (!empty($event_item['tag'])) echo "(" . htmlspecialchars($event_item['tag']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="horizontal-form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            
            <div class="horizontal-form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>
            </div>
            
            <div class="horizontal-form-group">
                <label for="reg_number">Reg Number <span class="required">*</span></label>
                <input type="text" id="reg_number" name="reg_number" value="<?php echo $reg_number; ?>" required>
            </div>
            
            <div class="horizontal-form-group">
                <label for="branch">Branch & Section <span class="required">*</span></label>
                <input type="text" id="branch" name="branch" value="<?php echo $branch; ?>" required>
            </div>
            <div class="horizontal-form-group">
                <label for="phone">Mobile Number <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>" required>
            </div>
            <div class="horizontal-form-group">
                <label>Year <span class="required">*</span></label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="year1" name="year" value="1st Year" <?php if ($year=="1st Year") echo "checked"; ?> required>
                        <label for="year1">1st Year</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="year2" name="year" value="2nd Year" <?php if ($year=="2nd Year") echo "checked"; ?>>
                        <label for="year2">2nd Year</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="year3" name="year" value="3rd Year" <?php if ($year=="3rd Year") echo "checked"; ?>>
                        <label for="year3">3rd Year</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="year4" name="year" value="4th Year" <?php if ($year=="4th Year") echo "checked"; ?>>
                        <label for="year4">4th Year</label>
                    </div>
                </div>
            </div>
            
            <div class="horizontal-form-group">
                <label>Gender <span class="required">*</span></label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="male" name="gender" value="Male" <?php if ($gender=="Male") echo "checked"; ?> required>
                        <label for="male">Male</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="female" name="gender" value="Female" <?php if ($gender=="Female") echo "checked"; ?>>
                        <label for="female">Female</label>
                    </div>
                </div>
            </div>
            
            <div class="horizontal-form-group">
                <label>Residence <span class="required">*</span></label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="hostler" name="residence" value="Hostler" <?php if ($residence=="Hostler") echo "checked"; ?> required>
                        <label for="hostler">Hostler</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="day_scholar" name="residence" value="Day Scholar" <?php if ($residence=="Day Scholar") echo "checked"; ?>>
                        <label for="day_scholar">Day Scholar</label>
                    </div>
                </div>
            </div>
            
            <div class="horizontal-form-group">
                <label for="committee">Committee <span class="required">*</span></label>
                <select id="committee" name="committee" required>
                    <option value="">-- Select a Committee --</option>
                    <option value="Traditional Events" <?php if ($committee=="Traditional Events") echo "selected"; ?>>Traditional Events</option>
                    <option value="Informal Events" <?php if ($committee=="Informal Events") echo "selected"; ?>>Informal Events</option>
                    <option value="Hospitality and Accommodation" <?php if ($committee=="Hospitality and Accommodation") echo "selected"; ?>>Hospitality and Accommodation</option>
                    <option value="Registration and Documentation" <?php if ($committee=="Registration and Documentation") echo "selected"; ?>>Registration and Documentation</option>
                    <option value="Logistics and Infrastructure" <?php if ($committee=="Logistics and Infrastructure") echo "selected"; ?>>Logistics and Infrastructure</option>
                    <option value="Writing and Certificates" <?php if ($committee=="Writing and Certificates") echo "selected"; ?>>Writing and Certificates</option>
                    <option value="Publicity" <?php if ($committee=="Publicity") echo "selected"; ?>>Publicity</option>
                    <option value="Outreach" <?php if ($committee=="Outreach") echo "selected"; ?>>Outreach</option>
                    <option value="Refreshments" <?php if ($committee=="Refreshments") echo "selected"; ?>>Refreshments</option>
                    <option value="Technical and Media" <?php if ($committee=="Technical and Media") echo "selected"; ?>>Technical and Media</option>
                    <option value="Transport" <?php if ($committee=="Transport") echo "selected"; ?>>Transport</option>
                    <option value="Finance and BR" <?php if ($committee=="Finance and BR") echo "selected"; ?>>Finance and BR (Budget & Resources)</option>
                    <option value="Photography" <?php if ($committee=="Photography") echo "selected"; ?>>Photography</option>
                    <option value="Website" <?php if ($committee=="Website") echo "selected"; ?>>Website</option>
                    <option value="Ceremonial" <?php if ($committee=="Ceremonial") echo "selected"; ?>>Ceremonial</option>
                    <option value="Medical and Safeguard" <?php if ($committee=="Medical and Safeguard") echo "selected"; ?>>Medical and Safeguard</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="experience">Explain why you think you are suitable for it, mention your past experience for the role, if any?</label>
                <textarea id="experience" name="experience"><?php echo $experience; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="ideas">Share any new ideas you have for the committee you chose and the event in general.</label>
                <textarea id="ideas" name="ideas"><?php echo $ideas; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="improvements">What improvements can be made in the event with respect to the committee you have applied?</label>
                <textarea id="improvements" name="improvements"><?php echo $improvements; ?></textarea>
            </div>
            
            <button type="submit">Submit Application</button>
        </form>
    </div>
    
    <div class="container" style="margin-top: 20px; background: transparent; box-shadow: none; padding: 0;">
        <a href="open_single_group.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Sports Portal</a>
    </div>
    
    <div class="overlay" id="settingsOverlay"></div>
    <div id="settingsForm">
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
            document.getElementById('settingsOverlay').style.display = 'block';
        }
        
        function goBack() {
            document.getElementById('mainContainer').style.display = 'block';
            document.getElementById('settingsForm').style.display = 'none';
            document.getElementById('settingsOverlay').style.display = 'none';
        }
        
        function showTab(tabId) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));
            
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        document.getElementById('settingsOverlay').addEventListener('click', function() {
            goBack();
        });
    </script>
</body>
</html>