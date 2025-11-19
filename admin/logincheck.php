<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get browser information
function get_browser_name() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unknown Browser";
    
    $browser_array = array(
        '/msie/i'      => 'Internet Explorer',
        '/firefox/i'   => 'Firefox',
        '/safari/i'    => 'Safari',
        '/chrome/i'    => 'Chrome',
        '/edge/i'      => 'Edge',
        '/opera/i'     => 'Opera',
        '/netscape/i'  => 'Netscape',
        '/maxthon/i'   => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i'    => 'Mobile Browser'
    );

    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
            break;
        }
    }
    
    return $browser;
}

// Function to get client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Check if admin_login_history table exists, if not create it
$check_history_table = $conn->query("SHOW TABLES LIKE 'admin_login_history'");
if ($check_history_table->num_rows == 0) {
    $create_history_table = "CREATE TABLE admin_login_history (
        history_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        admin_id INT(11),
        username VARCHAR(255) NOT NULL,
        login_time DATETIME NULL,
        logout_time DATETIME NULL,
        browser VARCHAR(255) NULL,
        ip_address VARCHAR(255) NULL,
        login_status VARCHAR(50) NULL,
        FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE SET NULL
    )";
    $conn->query($create_history_table);
}

// Check if logout is requested
if (isset($_GET['logout'])) {
    // Record logout if user was logged in
    if (isset($_SESSION['admin_username']) && isset($_SESSION['admin_id'])) {
        $admin_id = $_SESSION['admin_id'];
        $admin_username = $_SESSION['admin_username'];
        
        $logout_time = date('Y-m-d H:i:s');
        
        // Check if there's an open login session (no logout_time)
        $check_session = "SELECT history_id FROM admin_login_history 
                         WHERE admin_id = ? AND login_status = 'Logged In' 
                         AND logout_time IS NULL
                         ORDER BY login_time DESC LIMIT 1";
        $check_stmt = $conn->prepare($check_session);
        $check_stmt->bind_param("i", $admin_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 1) {
            // Update the existing login record with logout time
            $row = $check_result->fetch_assoc();
            $history_id = $row['history_id'];
            
            $update_sql = "UPDATE admin_login_history SET logout_time = ? WHERE history_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $logout_time, $history_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        
        $check_stmt->close();
    }
    
    session_destroy();  // Destroy session
    header("Location: index.php?message=logged_out");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = $conn->real_escape_string($_POST['username']);
    $admin_password = $conn->real_escape_string($_POST['password']);
    
    // Check if `admin` table exists
    $check_table = $conn->query("SHOW TABLES LIKE 'admin'");
    if ($check_table->num_rows == 0) {
        die("Error: Table 'admin' does not exist in the 'sports' database.");
    }
    
    // Query to check admin login
    $sql = "SELECT admin_id, email FROM admin WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $admin_username, $admin_password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get current time and browser/IP info
    $login_time = date('Y-m-d H:i:s');
    $browser = get_browser_name();
    $ip_address = get_client_ip();
    
    // If user exists, record login and redirect to dashboard
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $admin_id = $row['admin_id'];
        $admin_email = $row['email'];
        
        $_SESSION['admin_username'] = $admin_username;
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_email'] = $admin_email;
        
        // Record successful login in history table
        $login_status = 'Logged In';
        $insert_sql = "INSERT INTO admin_login_history 
                      (admin_id, username, login_time, browser, ip_address, login_status) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("isssss", $admin_id, $admin_username, $login_time, $browser, $ip_address, $login_status);
        $insert_stmt->execute();
        $insert_stmt->close();
        $stmt->close();
        
        header("Location: dashboard.php");
        exit();
    } else {
        // Record failed login attempt
        $login_status = 'Failed Login';
        
        // Try to get admin_id if username exists
        $check_user = "SELECT admin_id FROM admin WHERE username = ?";
        $check_stmt = $conn->prepare($check_user);
        $check_stmt->bind_param("s", $admin_username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 1) {
            $row = $check_result->fetch_assoc();
            $admin_id = $row['admin_id'];
            
            // Insert failed login attempt in history table
            $insert_sql = "INSERT INTO admin_login_history 
                          (admin_id, username, login_time, browser, ip_address, login_status) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("isssss", $admin_id, $admin_username, $login_time, $browser, $ip_address, $login_status);
            $insert_stmt->execute();
            $insert_stmt->close();
        } else {
            // If username doesn't exist, record the attempt WITHOUT the admin_id field
            $insert_sql = "INSERT INTO admin_login_history 
                          (username, login_time, browser, ip_address, login_status) 
                          VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sssss", $admin_username, $login_time, $browser, $ip_address, $login_status);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        
        $check_stmt->close();
        $stmt->close();
        
        header("Location: index.php?error=1");
        exit();
    }
}

$conn->close();
?>