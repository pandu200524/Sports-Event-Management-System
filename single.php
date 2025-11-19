<?php
session_start();
unset($_SESSION['payment_success']);
unset($_SESSION['payment_details']);
$host = "localhost";
$dbname = "sports";
$username = "root";
$password = ""; 
$port = 3307;
$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $reg_number = htmlspecialchars($_POST['reg_number'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $department = htmlspecialchars($_POST['department'] ?? '');
    $section = htmlspecialchars($_POST['section'] ?? '');
    $event = htmlspecialchars($_POST['event'] ?? '');
    $event_category = htmlspecialchars($_POST['event_category'] ?? '');
    $game_category = htmlspecialchars($_POST['game_category'] ?? '');
    $game = htmlspecialchars($_POST['game'] ?? '');
    $is_udgam = (strtolower($event) === 'udgam');
    $amount = $is_udgam ? 500 : 0;
    $stmt = $conn->prepare("INSERT INTO singleplayer (name, reg_number, email, phone, department, section, event, event_category, game_category, game, amount, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $payment_status = $is_udgam ? 'pending' : 'not_required';
    $stmt->bind_param("ssssssssssds", $name, $reg_number, $email, $phone, $department, $section, $event, $event_category, $game_category, $game, $amount, $payment_status);
    if ($stmt->execute()) {
        $registration_id = $stmt->insert_id;
        $_SESSION['registration_data'] = [
            'id' => $registration_id,
            'type' => 'single',
            'name' => $name,
            'reg_number' => $reg_number,
            'email' => $email,
            'phone' => $phone,
            'department' => $department,
            'section' => $section,
            'event' => $event,
            'event_category' => $event_category,
            'game_category' => $game_category,
            'game' => $game,
            'amount' => $amount,
            'is_udgam' => $is_udgam
        ];
        header("Location: payment.php?from_registration=1");
        exit();
    } else {
        echo "<script>
            alert('Registration failed: " . addslashes($stmt->error) . "');
            window.location.href = 'registration.php';
        </script>";
    }
    $stmt->close();
}
$conn->close();
?>