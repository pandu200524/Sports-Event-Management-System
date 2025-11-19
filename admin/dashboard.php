<?php
session_start();

if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];

$sql = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$admin_email = "";
$admin_name = "";

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $admin_email = $row['email'];
    $admin_name = isset($row['name']) ? $row['name'] : "Admin User";
} else {
    if ($admin_id == 1) {
        $admin_email = "sports@srmap.edu.in";
        $admin_name = "Sports Council";
    } else if ($admin_id == 2) {
        $admin_email = "sportevents@srmap.edu.in";
        $admin_name = "Event Organizer";
    } else {
        $admin_email = "admin@srmap.edu.in";
        $admin_name = "Administrator";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: Arial, sans-serif; background-color: #f4f4f9; min-height: 100vh; display: flex;}
        .sidebar {width: 250px; background-color: #484622; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding: 15px;}
        .sidebar-header {text-align: center; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);}
        .srm-logo {width: 120px; height: 40px; margin-top:5px; margin-bottom: 10px;}
        .nav-menu {list-style: none; padding: 15px 0;}
        .nav-item {padding: 12px 20px; display: flex; align-items: center; cursor: pointer; transition: background-color 0.2s;}
        .nav-item:hover {background-color: rgb(210, 152, 44);}
        .nav-item i {margin-right: 10px; width: 20px;}
        .main-content {flex: 1; margin-left: 250px; display: flex; flex-direction: column; min-height: 100vh; width: calc(100% - 250px);}
        header {background-color: #484622; color: white; padding: 20px; display: flex; justify-content: flex-end;}
        .logout-btn {background-color: rgb(210, 152, 44); color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none;}
        .logout-btn:hover {background-color: orange;}
        footer {background-color: #484622; color: white; padding: 15px; text-align: center; margin-top: auto;}
        .profile-container {display: flex; align-items: center; justify-content: center; margin-top: 10px;}
        .profile-circle {width: 60px; height: 60px; background-color: #3a3a1e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px;}
        .profile-circle i {font-size: 24px;}
        .welcome-message {text-align: left;}
        .welcome-message h3 {font-size: 18px; margin: 0; color: #ddd;}
        .welcome-message h1 {font-size: 22px; color: white; margin: 0;}
        #adminProfile, #eventManagement, #announcementsSection, #achievementsSection, #changePasswordForm, #viewreg, #payments, #od, #accesslog, #matchupsSection {display: none;}
        .container {margin: 50px auto; width: 80%; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;}
        .card {background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;}
        .card button {background-color: #484622; color: white; padding: 12px 24px; border: none; cursor: pointer; border-radius: 5px; width: 100%; font-size: 16px; transition: background-color 0.1s;}
        #adminProfile {margin-top: 20px; border-collapse: collapse; width: 50%; height: 300px; margin-top:40px; margin-left: 50px; padding: 40px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);}
        #adminProfile th, #adminProfile td {padding: 12px; text-align: left; border-bottom: 1px solid #ddd;}
        #adminProfile th {background-color: rgb(210, 152, 44); color: white;}
        #adminProfile td {background-color: #f9f9f9;}
        #changePasswordForm {display: none; padding: 20px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 40%; margin: auto;}
        #changePasswordForm input {width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;}
        #changePasswordForm button {width: 100%;}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="https://srmap.edu.in/file/2019/12/White.png" alt="SRM University Logo" class="srm-logo">
    </div>
    <div class="profile-container" onclick="toggleProfile()">
        <div class="profile-circle"><i class="fas fa-user"></i></div>
        <div class="welcome-message">
            <h3>Welcome,</h3>
            <h1><?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
        </div>
    </div>
    <ul class="nav-menu">
        <li class="nav-item" onclick="toggleSection('eventManagement')"><i class="fas fa-calendar-alt"></i> <span>Event Management</span></li>
        <li class="nav-item" onclick="toggleSection('od')"><i class="fa-solid fa-clipboard-user"></i><span>OD Attendance</span></li>
        <li class="nav-item" onclick="toggleSection('announcementsSection')"><i class="fas fa-bullhorn"></i> <span>Announcements</span></li>
        <li class="nav-item" onclick="toggleSection('viewreg')"><i class="fa-solid fa-graduation-cap"></i><span>View Registrations</span></li>
        <li class="nav-item" onclick="toggleSection('payments')"><i class="fas fa-money-bill-wave"></i><span>Set Price</span></li>
        <li class="nav-item" onclick="toggleSection('matchupsSection')"><i class="fas fa-random"></i><span>Matchups</span></li>
        <li class="nav-item" onclick="toggleSection('achievementsSection')"><i class="fas fa-trophy"></i> <span>Achievements</span></li>
        <li class="nav-item" onclick="toggleSection('changePasswordForm')"><i class="fas fa-cog"></i> <span>Settings</span></li>
    </ul>
</div>

<div class="main-content">
    <header>
        <a href="logincheck.php?logout=1" class="logout-btn">Logout</a>
    </header>

    <div id="adminProfile">
        <h2>Admin Profile Details</h2><br>
        <table>
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($admin_username); ?></td>
            </tr>
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($admin_id); ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($admin_name); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($admin_email); ?></td>
            </tr>
        </table>
    </div>
        
    <div id="eventManagement">
        <div class="container">
            <div class="card"><h2>Add Event</h2><a href="add.php"><br><button>Add New Event</button></a></div>
            <div class="card"><h2>Modify Event</h2><a href="edit.php"><br><button>Edit Existing Event</button></a></div>
            <div class="card"><h2>Delete Event</h2><a href="delete.php"><br><button>Delete Event</button></a></div>
        </div>
    </div>

    <div id="od">
        <iframe src="giveod.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>

    <div id="announcementsSection">
        <iframe src="addnotif.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>
    
    <div id="viewreg">
        <iframe src="viewreg.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>

    <div id="achievementsSection">
        <iframe src="achievements.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>
    
    <div id="payments">
        <iframe src="payments.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>
    
    <div id="matchupsSection">
        <iframe src="generate_matchups.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>

    <div id="changePasswordForm">
        <h2>Change Password</h2>
        <form action="changepassword.php" method="post">
            <input type="password" name="old_password" placeholder="Old Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Change Password</button>
        </form>
    </div>

    <footer>
        <p>© 2025 SRM University AP - All Rights Reserved.</p>
    </footer>
</div>
<script>
    function toggleProfile() {
        let profileSection = document.getElementById('adminProfile');
        profileSection.style.display = (profileSection.style.display === 'block') ? 'none' : 'block';
        
        let sections = ['eventManagement', 'announcementsSection', 'viewreg', 'payments', 'achievementsSection', 'changePasswordForm', 'od', 'matchupsSection'];
        sections.forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
    }
    
    function toggleSection(sectionId) {
        let sections = ['adminProfile', 'eventManagement', 'announcementsSection', 'viewreg', 'payments', 'achievementsSection', 'changePasswordForm', 'od', 'matchupsSection'];
        
        sections.forEach(id => {
            document.getElementById(id).style.display = (id === sectionId) ? 'block' : 'none';
        });
    }
    
    window.onload = function() {
        toggleSection('adminProfile');
    };
</script>
</body>
</html>