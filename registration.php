<?php
session_start(); 
include("connection.php");

// Generate captcha if not exists
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = chr(rand(65, 90)) . rand(1000, 9999); 
}

// Handle form submission
if (isset($_POST['login_submit'])) {
    $reg_number = mysqli_real_escape_string($conn, $_POST['reg_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $captchaInput = $_POST['captchaInput']; 
    
    // Validate captcha
    if ($captchaInput !== $_SESSION['captcha']) {
        echo '<script>
                alert("Invalid CAPTCHA. Please try again.");
                window.location.href = "registration.php";
              </script>';
        // Reset captcha
        unset($_SESSION['captcha']); 
        $_SESSION['captcha'] = chr(rand(65, 90)) . rand(1000, 9999); 
        exit();
    }
    
    // Check if user exists
    $sql = "SELECT * FROM registration WHERE reg_number = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $reg_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row) {
        // User exists, verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['reg_number'] = $reg_number;
            $_SESSION['logged_in'] = true;
            unset($_SESSION['captcha']);
            echo '<script>
                    alert("Login Successful!");
                    window.location.href = "open_single_group.php";
                  </script>';
            exit();
        } else {
            echo '<script>
                    alert("Incorrect Password. Please try again.");
                    window.location.href = "registration.php";
                  </script>';
            exit();
        }
    } else {
        // User doesn't exist, register new user
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO registration (reg_number, password) VALUES(?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "ss", $reg_number, $hash);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['reg_number'] = $reg_number;
            $_SESSION['logged_in'] = true;
            unset($_SESSION['captcha']);
            echo '<script>
                    alert("Registration Successful!");
                    window.location.href = "open_single_group.php";
                  </script>';
            exit();
        } else {
            echo '<script>
                    alert("Error while registering. Please try again. Error: ' . mysqli_error($conn) . '");
                    window.location.href = "registration.php";
                  </script>';
            exit();
        }
    }
}

// Debug function for logging
function debug_to_file($data) {
    $file = fopen("debug_log.txt", "a");
    fwrite($file, date("Y-m-d H:i:s") . ": " . print_r($data, true) . "\n");
    fclose($file);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRM University Login</title>
  <link rel="stylesheet" href="css/registration.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="registration.js" defer></script>
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
      <h2>Event Login</h2>
      <form action="registration.php" method="post">
        <label for="reg_number">Application Number</label>
        <input type="text" id="reg_number" name="reg_number" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        
        <div class="captcha-box"><?php echo $_SESSION['captcha']; ?></div>
        <label for="captchaInput">Enter Captcha Text</label>
        <input type="text" id="captchaInput" name="captchaInput" required>
        
        <button type="submit" name="login_submit">Login</button>
      </form>
      <p>If you are a Student and this is your first login, use your Register Number
        / Application Number as your User ID, with your date of birth (Format DDMMYYYY, e.g., 03121990) 
        as your password.</p>
    </div>
  </main>
  <footer>
    <p>&copy; 2025 SRM University AP - All Rights Reserved</p>
    <div class="social-media">
      <a href="https://www.facebook.com/SRMUAP/" target="_blank"><i class="fab fa-facebook"></i></a>
      <a href="https://www.linkedin.com/school/srmuap/" target="_blank"><i class="fab fa-linkedin"></i></a>
      <a href="https://www.instagram.com/sportscouncilsrmuap/" target="_blank"><i class="fab fa-instagram"></i></a>
      <a href="https://x.com/SRMUAP" target="_blank"><i class="fab fa-twitter"></i></a>
      <a href="https://www.youtube.com/c/SRMUniversityAP" target="_blank"><i class="fab fa-youtube"></i></a>
    </div>
  </footer>
</body>
</html>