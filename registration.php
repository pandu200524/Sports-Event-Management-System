<?php
session_start(); // Start the session
include("connection.php");

// Generate a random CAPTCHA only if it is not already set
if (!isset($_SESSION['captcha'])) {
    $captcha = chr(rand(65, 90)) . rand(1000, 9999); // Example: A1234
    $_SESSION['captcha'] = $captcha; // Store CAPTCHA in session for validation
}

if (isset($_POST['login_submit'])) {
    $app_number = mysqli_real_escape_string($conn, $_POST['app_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $captchaInput = $_POST['captchaInput']; // Get user-entered CAPTCHA

    // Validate CAPTCHA
    if ($captchaInput !== $_SESSION['captcha']) {
        echo '<script>
                alert("Invalid CAPTCHA. Please try again.");
                window.location.href = "registration.php";
              </script>';
        
        // Regenerate CAPTCHA after failed attempt
        unset($_SESSION['captcha']);
        $captcha = chr(rand(65, 90)) . rand(1000, 9999);
        $_SESSION['captcha'] = $captcha;
        exit();
    }

    // Check if the application number exists
    $sql = "SELECT * FROM registration WHERE app_number='$app_number'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        // If user exists, verify password
        if (password_verify($password, $row['password'])) {
            echo '<script>
                    window.open("open_single_group.php", "_blank");
                    window.location.href = "open_single_group.php"; // Redirect to another page after opening the new tab
                  </script>';
        } else {
            echo '<script>
                    alert("Incorrect Password");
                    window.location.href = "registration.php";
                  </script>';
        }
    } else {
        // Insert new user if application number is not found
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO registration (app_number, password) VALUES('$app_number', '$hash')";

        if (mysqli_query($conn, $insert_sql)) {
            echo '<script>
                    window.open("open_single_group.php", "_blank");
                    window.location.href = "open_single_group.php"; // Redirect to another page after opening the new tab
                  </script>';
        } else {
            echo '<script>
                    alert("Error while registering. Please try again.");
                    window.location.href = "registration.php";
                  </script>';
        }
    }

    // Regenerate CAPTCHA after successful login/registration
    unset($_SESSION['captcha']);
    $captcha = chr(rand(65, 90)) . rand(1000, 9999);
    $_SESSION['captcha'] = $captcha;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRM University Login</title>
  <link rel="stylesheet" href="registration.css">
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
      <form action="" method="post">
        <label for="app_number">Application Number</label>
        <input type="text" id="app_number" name="app_number" required>
      
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      
        <!-- Display CAPTCHA -->
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
  </footer>
</body>
</html>