<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "Rev2005@!!";
$dbname = "srm_sports";
$port=4307;

// Establishing connection
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Processing form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs to prevent SQL injection
    $appNumber = mysqli_real_escape_string($conn, $_POST['appNumber']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check user credentials
    $sql = "SELECT * FROM users WHERE app_number = '$appNumber' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<script>alert('Login successful!');</script>";
    } else {
        echo "<script>alert('Invalid application number or password.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRM University Login</title>
  <link rel="stylesheet" href="registration.css">
</head>
<body>
  <header class="header">
    <img src="https://srmap.edu.in/file/2019/12/White.png?x42812" alt="SRM AP Logo">
  </header>

  <main>
    <div class="content">
      <h1>SRM University, AP - Andhra Pradesh</h1>
      <div class="info">
        <h2><b>SRM AP SPORTS PORTAL</b></h2>
        <p>Welcome to the Sports Club at SRM University-AP, Andhra Pradesh—a vibrant community of talented sportspersons 
          dedicated to fostering team spirit, leadership, and fitness. Our club organizes numerous competitions and fun
          activities throughout the year, promoting healthy sporting habits and equal participation of boys and girls
          across all disciplines. With expert faculty mentors and specialized trainers, we help students hone their skills,
          embrace the values of time, precision, and teamwork, and excel in sports at national and international levels. 
          Join us to explore your passion for sports, overcome stress, and build lasting camaraderie!</p>
        <p>For any technical support, email to 
          <a href="mailto:sportsevent.helpdesk@srmap.edu.in">sportsevent.helpdesk@srmap.edu.in</a></p>
      </div>
    </div>
    <div class="login">
      <h2>Event Registration</h2>
      <form action="registration.php" method="post">
        <label for="appNumber">Application Number / Register Number</label>
        <input type="text" id="appNumber" name="appNumber" required>
      
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      
        <div class="captcha-box" id="captcha">E2753</div>
        <label for="captchaInput">Enter Captcha Text</label>
        <input type="text" id="captchaInput" name="captchaInput" required>
      
        <button type="submit">Login</button>
      </form>
      <p>If you are a Student and this is your first login, use your Register Number
        / Application Number as your User ID, with your date of birth (Format DDMMYYYY, e.g., 03121990) 
        as your password.</p>
    </div>
  </main>
  <footer>
    <p>&copy; 2025 SRM University AP - All Rights Reserved</p>
  </footer>
</body>
</html>

