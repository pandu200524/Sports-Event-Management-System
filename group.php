<?php
session_start();
include("connection.php");

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: registration.php");
    exit();
}

// Clear any previous payment status when starting new registration
unset($_SESSION['payment_success']);
unset($_SESSION['payment_details']);

// Get reg_number from session
$reg_number = $_SESSION['reg_number'] ?? '';

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
    
    // Check if it's an Udgam event (we'll keep this for payment amount calculation)
    $is_udgam = (strtolower($event) === 'udgam');
    $amount = $is_udgam ? 1000 : 0;
    $payment_status = 'pending'; // Set to pending for all registrations
    $registration_date = date('Y-m-d H:i:s');
    $transaction_id = '';
    $payment_date = '';
    
    // Generate a unique group ID
    $group_id = uniqid('GRP_');
    
    // Process team member data
    $team_member_names = isset($_POST['member_name']) ? $_POST['member_name'] : [];
    $team_member_reg_numbers = isset($_POST['member_reg']) ? $_POST['member_reg'] : [];
    
    // Convert arrays to comma-separated strings
    $team_member_name_str = implode(',', array_map('htmlspecialchars', $team_member_names));
    $team_member_reg_number_str = implode(',', array_map('htmlspecialchars', $team_member_reg_numbers));
    
    // Insert into groupplayers table
    $stmt = $conn->prepare("INSERT INTO groupplayers (name, reg_number, email, phone, department, section, event, event_category, game_category, game, team_member_name, team_member_reg_number, amount, payment_status, group_id, registration_date, transaction_id, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssssssssssssdsssss", 
        $name, 
        $reg_number, 
        $email, 
        $phone, 
        $department, 
        $section, 
        $event, 
        $event_category, 
        $game_category, 
        $game, 
        $team_member_name_str, 
        $team_member_reg_number_str,
        $amount,
        $payment_status,
        $group_id,
        $registration_date,
        $transaction_id,
        $payment_date
    );
    
    if ($stmt->execute()) {
        $registration_id = $stmt->insert_id;
        
        // Store data for payment page
        $_SESSION['registration_data'] = [
            'id' => $registration_id,
            'type' => 'group',
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
            'is_udgam' => $is_udgam,
            'group_id' => $group_id,
            'team_members' => array_map(function($name, $reg) {
                return ['name' => $name, 'reg_number' => $reg];
            }, $team_member_names, $team_member_reg_numbers)
        ];
        
        // Always redirect to payment.php
        header("Location: payment.php?from_registration=1");
        exit();
    } else {
        echo "<script>alert('Registration failed: " . addslashes($stmt->error) . "'); window.location.href = 'home.php';</script>";
    }
    
    $stmt->close();
}

$conn->close();
?>