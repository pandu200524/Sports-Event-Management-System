<?php
session_start();
if (isset($_GET['from_registration'])) {
    unset($_SESSION['payment_success']);
    unset($_SESSION['payment_details']);
}
if (!isset($_SESSION['registration_data'])) {
    $_SESSION['registration_data'] = isset($_POST['registration_data']) ? $_POST['registration_data'] : [];
}
$registration = $_SESSION['registration_data'];
$is_udgam_event = ($registration['event'] ?? '') === 'Udgam';
$host = "localhost"; $dbname = "sports"; $username = "root"; $password = ""; $port = 3307;
$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }

$check_payments_table_sql = "CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY, reg_number VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL,
    event VARCHAR(50) NOT NULL, event_category VARCHAR(50), game VARCHAR(50) NOT NULL,
    game_category VARCHAR(50) NOT NULL, registration_type VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL, payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) NOT NULL, payment_date DATETIME NOT NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending', email VARCHAR(100),
    phone VARCHAR(20), registration_id INT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($check_payments_table_sql);

$check_group_payments_table_sql = "CREATE TABLE IF NOT EXISTS group_payments (
    id INT AUTO_INCREMENT PRIMARY KEY, reg_number VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL,
    event VARCHAR(50) NOT NULL, event_category VARCHAR(50), game VARCHAR(50) NOT NULL,
    game_category VARCHAR(50) NOT NULL, registration_type VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL, payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) NOT NULL, payment_date DATETIME NOT NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending', email VARCHAR(100),
    phone VARCHAR(20), registration_id INT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($check_group_payments_table_sql);

$group_players = [];
if (isset($registration['type']) && $registration['type'] === 'group') {
    if (isset($registration['team_members']) && is_array($registration['team_members'])) {
        $group_players = $registration['team_members'];
    } 
    else if (isset($registration['reg_number'])) {
        $players_sql = "SELECT * FROM groupplayers WHERE reg_number = ?";
        $players_stmt = $conn->prepare($players_sql);
        $players_stmt->bind_param("s", $registration['reg_number']);
        $players_stmt->execute();
        $result = $players_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $group_data = $result->fetch_assoc();
            
            if (!empty($group_data['team_member_name']) && !empty($group_data['team_member_reg_number'])) {
                $member_names = explode(',', $group_data['team_member_name']);
                $member_regs = explode(',', $group_data['team_member_reg_number']);
                
                for ($i = 0; $i < count($member_names); $i++) {
                    if (isset($member_names[$i]) && isset($member_regs[$i])) {
                        $group_players[] = [
                            'name' => $member_names[$i],
                            'reg_number' => $member_regs[$i]
                        ];
                    }
                }
            }
            
            if (isset($group_data['group_id'])) {
                $registration['group_id'] = $group_data['group_id'];
                $_SESSION['registration_data'] = $registration;
            }
        }
        $players_stmt->close();
    }
}

$amount = 0;
if ($is_udgam_event) {
    $game = $conn->real_escape_string($registration['game'] ?? '');
    $type = ($registration['type'] ?? '') === 'group' ? 'group' : 'single';
    $price_sql = "SELECT price FROM game_prices WHERE game_name = '$game' AND type = '$type'";
    $price_result = $conn->query($price_sql);
    if (!$price_result) {
        $amount = $type === 'group' ? 1000 : 500;
    } else if ($price_result->num_rows > 0) {
        $price_row = $price_result->fetch_assoc();
        $amount = $price_row['price'];
    } else {
        $amount = $type === 'group' ? 1000 : 500;
    }
    $registration['amount'] = $amount;
    $_SESSION['registration_data'] = $registration;
}

$table_check_sql = "SHOW TABLES LIKE 'game_prices'";
$table_result = $conn->query($table_check_sql);
if ($table_result->num_rows == 0) {
    $create_table_sql = "CREATE TABLE IF NOT EXISTS game_prices (
        id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL,
        game_name VARCHAR(100) NOT NULL, type VARCHAR(20) NOT NULL,
        price DECIMAL(10,2) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($create_table_sql);
    $insert_prices_sql = "INSERT INTO game_prices (name, game_name, type, price) VALUES 
        ('Udgam', 'Cricket', 'group', 1000), ('Udgam', 'Cricket', 'single', 500),
        ('Udgam', 'Football', 'group', 1000), ('Udgam', 'Football', 'single', 500),
        ('Udgam', 'Basketball', 'group', 1000), ('Udgam', 'Basketball', 'single', 500)";
    $conn->query($insert_prices_sql);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $is_udgam_event && isset($_POST['pay_method'])) {
    $pay_method = $_POST['pay_method'] ?? 'card';
    $payment_date = date("Y-m-d H:i:s");
    $transaction_id = "UDGAM" . time();
    
    $_SESSION['payment_details'] = [
        'amount' => $amount,
        'method' => $pay_method,
        'transaction_id' => $transaction_id,
        'payment_date' => $payment_date
    ];
    
    if ($registration['type'] === 'group') {
        $find_id_sql = "SELECT id FROM groupplayers WHERE reg_number = ? LIMIT 1";
        $find_id_stmt = $conn->prepare($find_id_sql);
        $find_id_stmt->bind_param("s", $registration['reg_number']);
        $find_id_stmt->execute();
        $id_result = $find_id_stmt->get_result();
        
        if ($id_result->num_rows > 0) {
            $id_row = $id_result->fetch_assoc();
            $registration_id = $id_row['id'];
        } else {
            $_SESSION['error'] = "Registration not found. Please register again.";
            header("Location: payment.php");
            exit();
        }
        $find_id_stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO group_payments 
        (reg_number, name, event, event_category, game, game_category, registration_type, 
         amount, payment_method, transaction_id, payment_date, payment_status, email, phone, registration_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?, ?, ?)");
        
        $reg_number = $registration['reg_number'] ?? '';
        $name = $registration['name'] ?? '';
        $event = $registration['event'] ?? '';
        $event_category = $registration['event_category'] ?? '';
        $game = $registration['game'] ?? '';
        $game_category = $registration['game_category'] ?? '';
        $reg_type = $registration['type'] ?? 'group';
        $email = $registration['email'] ?? '';
        $phone = $registration['phone'] ?? '';
        
        $stmt->bind_param("sssssssdsssssi", 
            $reg_number, $name, $event, $event_category, $game, $game_category, $reg_type,
            $amount, $pay_method, $transaction_id, $payment_date, $email, $phone, $registration_id
        );
    } else {
        $find_id_sql = "SELECT id FROM singleplayer WHERE reg_number = ? LIMIT 1";
        $find_id_stmt = $conn->prepare($find_id_sql);
        $find_id_stmt->bind_param("s", $registration['reg_number']);
        $find_id_stmt->execute();
        $id_result = $find_id_stmt->get_result();
        
        if ($id_result->num_rows > 0) {
            $id_row = $id_result->fetch_assoc();
            $registration_id = $id_row['id'];
        } else {
            $_SESSION['error'] = "Registration not found. Please register again.";
            header("Location: payment.php");
            exit();
        }
        $find_id_stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO payments 
        (reg_number, name, event, event_category, game, game_category, registration_type, 
         amount, payment_method, transaction_id, payment_date, payment_status, email, phone, registration_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?, ?, ?)");
        
        $reg_number = $registration['reg_number'] ?? '';
        $name = $registration['name'] ?? '';
        $event = $registration['event'] ?? '';
        $event_category = $registration['event_category'] ?? '';
        $game = $registration['game'] ?? '';
        $game_category = $registration['game_category'] ?? '';
        $reg_type = $registration['type'] ?? 'single';
        $email = $registration['email'] ?? '';
        $phone = $registration['phone'] ?? '';
        
        $stmt->bind_param("sssssssdsssssi", 
            $reg_number, $name, $event, $event_category, $game, $game_category, $reg_type,
            $amount, $pay_method, $transaction_id, $payment_date, $email, $phone, $registration_id
        );
    }
    
    if ($stmt->execute()) {
        $payment_id = $conn->insert_id;
        
        if ($registration['type'] === 'group') {
            $update_stmt = $conn->prepare("UPDATE groupplayers SET payment_status = 'paid', amount = ?, transaction_id = ?, payment_date = ? WHERE id = ?");
            $update_stmt->bind_param("dssi", $amount, $transaction_id, $payment_date, $registration_id);
        } else {
            $update_stmt = $conn->prepare("UPDATE singleplayer SET payment_status = 'paid', amount = ?, transaction_id = ?, payment_date = ? WHERE id = ?");
            $update_stmt->bind_param("dssi", $amount, $transaction_id, $payment_date, $registration_id);
        }
        
        $update_stmt->execute();
        $update_stmt->close();
        $_SESSION['payment_success'] = true;
        $_SESSION['success_message'] = "✅ Payment successful! Transaction ID: <strong>" . $transaction_id . "</strong>";
        header("Location: payment.php");
        exit();
    } else {
        $_SESSION['error'] = "Error processing payment: " . $conn->error;
        header("Location: payment.php");
        exit();
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && !$is_udgam_event) {
    $payment_date = date("Y-m-d H:i:s");
    $transaction_id = "FREE" . time();
    
    if ($registration['type'] === 'group') {
        $find_id_sql = "SELECT id FROM groupplayers WHERE reg_number = ? LIMIT 1";
        $table_name = "group_payments";
        $update_table = "groupplayers";
    } else {
        $find_id_sql = "SELECT id FROM singleplayer WHERE reg_number = ? LIMIT 1";
        $table_name = "payments";
        $update_table = "singleplayer";
    }
    
    $find_id_stmt = $conn->prepare($find_id_sql);
    $find_id_stmt->bind_param("s", $registration['reg_number']);
    $find_id_stmt->execute();
    $id_result = $find_id_stmt->get_result();
    
    if ($id_result->num_rows > 0) {
        $id_row = $id_result->fetch_assoc();
        $registration_id = $id_row['id'];
    } else {
        $_SESSION['error'] = "Registration not found. Please register again.";
        header("Location: payment.php");
        exit();
    }
    $find_id_stmt->close();
    
    $sql = "INSERT INTO $table_name 
        (reg_number, name, event, event_category, game, game_category, registration_type, 
         amount, payment_method, transaction_id, payment_date, payment_status, email, phone, registration_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'free', ?, ?, 'completed', ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    $reg_number = $registration['reg_number'] ?? '';
    $name = $registration['name'] ?? '';
    $event = $registration['event'] ?? '';
    $event_category = $registration['event_category'] ?? '';
    $game = $registration['game'] ?? '';
    $game_category = $registration['game_category'] ?? '';
    $reg_type = $registration['type'] ?? ($registration['type'] === 'group' ? 'group' : 'single');
    $email = $registration['email'] ?? '';
    $phone = $registration['phone'] ?? '';
    
    $stmt->bind_param("sssssssssssi", 
        $reg_number, $name, $event, $event_category, $game, $game_category, $reg_type,
        $transaction_id, $payment_date, $email, $phone, $registration_id
    );
    
    if ($stmt->execute()) {
        $update_stmt = $conn->prepare("UPDATE $update_table SET payment_status = 'completed' WHERE id = ?");
        $update_stmt->bind_param("i", $registration_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        $_SESSION['payment_success'] = true;
        $_SESSION['payment_details'] = [
            'amount' => 0,
            'method' => 'free',
            'transaction_id' => $transaction_id,
            'payment_date' => $payment_date
        ];
        $_SESSION['success_message'] = "✅ Registration completed successfully! Transaction ID: <strong>" . $transaction_id . "</strong>";
        unset($_SESSION['registration_data']);
        header("Location: payment.php");
        exit();
    } else {
        $_SESSION['error'] = "Error processing registration: " . $stmt->error;
        header("Location: payment.php");
        exit();
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_udgam_event ? "Pay for Udgam Event" : "Registration Complete" ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;margin:0;padding:0;background:linear-gradient(135deg,#f5f7fa,#e4e8ed);min-height:100vh;display:flex;flex-direction:column}header,footer{background-color:#2c3e50;color:white;padding:15px 0;text-align:center}.container{max-width:800px;margin:20px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.1);flex-grow:1}@media print{body *{visibility:hidden}.printable-form,.printable-form *{visibility:visible}.printable-form{position:absolute;left:0;top:0;width:100%;height:100%;padding:20px;margin:0;background:white;border:none;box-shadow:none}.no-print{display:none !important}}.receipt-container{max-width:800px;margin:0 auto;padding:20px;background:white;box-shadow:0 0 20px rgba(0,0,0,0.1);border-radius:8px;position:relative;overflow:hidden}.receipt-header{text-align:center;margin-bottom:30px;padding-bottom:20px;border-bottom:2px solid #f1f1f1;position:relative}.receipt-header h2{color:#2c3e50;margin-bottom:5px;font-weight:700}.receipt-header h3{color:#3498db;margin-bottom:10px;font-weight:600}.receipt-header p{color:#7f8c8d;margin-bottom:5px}.university-logo{max-width:120px;margin-bottom:15px}.receipt-body{display:flex;flex-wrap:wrap;margin-bottom:30px}.receipt-section{flex:1;min-width:10px;margin-bottom:20px}.receipt-section h4{color:#2c3e50;border-bottom:1px solid #eee;padding-bottom:8px;margin-bottom:15px;font-size:18px}.receipt-row{align-items:center;justify-content:space-between;font-weight:bold;padding:10px 0;margin-bottom:10px}.receipt-label{font-weight:600;color:#7f8c8d;flex:1}.receipt-value{flex:1;text-align:right;color:#2c3e50}.transaction-details{background:#f8f9fa;padding:20px;border-radius:8px;margin-bottom:20px}.transaction-id{font-size:20px;color:#27ae60;font-weight:700;text-align:center;margin:15px 0}.team-table{width:100%;border-collapse:collapse;margin-top:15px}.team-table th{background:#3498db;color:white;padding:10px;text-align:left}.team-table td{padding:10px;border-bottom:1px solid #eee}.team-table tr:nth-child(even){background:#f8f9fa}.receipt-footer{text-align:center;margin-top:30px;padding-top:20px;border-top:2px solid #f1f1f1;color:#7f8c8d;font-size:14px}.receipt-footer p{margin-bottom:5px}.watermark{position:absolute;opacity:0.1;font-size:120px;color:#3498db;transform:rotate(-30deg);z-index:0;top:30%;left:10%;pointer-events:none}.payment-amount{font-size:24px;font-weight:700;color:#27ae60;text-align:center;margin:20px 0;padding:10px;background:#f8f9fa;border-radius:8px}.status-badge{display:inline-block;padding:5px 10px;background:#27ae60;color:white;border-radius:20px;font-size:14px;font-weight:600}.payment-summary{background:#f8f9fa;padding:20px;border-radius:8px;margin-bottom:30px;text-align:left}.success-message{background-color:#d1e7dd;color:#0f5132;border-left:5px solid #198754;padding:15px;border-radius:8px;margin-bottom:20px}.alert-danger{background-color:#f8d7da;color:#721c24;border-left:5px solid #f5c6cb;padding:15px;border-radius:8px;margin-bottom:20px}.payment-methods{margin-bottom:20px}.payment-method{display:flex;align-items:center;margin-bottom:15px;padding:15px;border:1px solid #ddd;border-radius:5px;cursor:pointer;transition:all 0.3s}.payment-method:hover{border-color:#484622;background-color:#f5f5f5}.payment-method input{margin-right:15px}.payment-method i{font-size:24px;margin-right:15px;color:#484622}.payment-details{display:none;padding:15px;background:#f8f9fa;border-radius:5px;margin-top:10px}.payment-details.active{display:block}.form-group{margin-bottom:15px;text-align:left}.form-group label{display:block;margin-bottom:5px;font-weight:bold}.form-group input,.form-group select{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px;box-sizing:border-box}.form-group input[readonly]{background-color:#f5f5f5}button{background-color:#484622;color:white;border:none;padding:12px 20px;border-radius:4px;cursor:pointer;font-size:16px;width:100%;transition:background-color 0.3s}button:hover{background-color:#3a3a1c}.back-button{display:inline-block;margin-top:15px;padding:10px 15px;background-color:#6c757d;color:white;text-decoration:none;border-radius:4px}.back-button:hover{background-color:#5a6268}.registration-complete{text-align:center;padding:30px}.registration-complete h3{color:#28a745;margin-bottom:20px}.registration-complete .icon{font-size:80px;color:#28a745;margin-bottom:20px}.details-box{background:#f8f9fa;border-radius:10px;padding:20px;margin:20px 0;text-align:left}.section-gap{height:20px;margin:20px 0;border-top:1px solid #ddd}</style>
</head>
<body>
    <header class="no-print">
        <h1>Sports Portal - <?= $is_udgam_event ? "Payment" : "Registration" ?></h1>
    </header>
    <div class="container no-print">
        <?php if (isset($_SESSION['payment_success']) && $_SESSION['payment_success']): ?>
            <div class="registration-complete printable-form">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h3>Payment Successful!</h3>
                <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message"><?= $_SESSION['success_message'] ?></div>
                <?php endif; ?>
                <div class="details-box">
                    <h5>Transaction Details</h5>
                    <div class="receipt-row">
                        <span class="receipt-label">Amount:</span>
                        <span class="receipt-value">₹<?= number_format($_SESSION['payment_details']['amount'] ?? 0, 2) ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Payment Method:</span>
                        <span class="receipt-value"><?= ucfirst($_SESSION['payment_details']['method'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Transaction ID:</span>
                        <span class="receipt-value"><?= htmlspecialchars($_SESSION['payment_details']['transaction_id'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Date:</span>
                        <span class="receipt-value"><?= isset($_SESSION['payment_details']['payment_date']) ? date('d/m/Y H:i:s', strtotime($_SESSION['payment_details']['payment_date'])) : '' ?></span>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <button onclick="window.print()" class="btn btn-success me-3">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <a href="open_single_group.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Registration
                    </a>
                </div>
            </div>
        <?php elseif ($is_udgam_event && !isset($_SESSION['payment_success'])): ?>
            <h2>Pay for Udgam Event</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <div class="payment-summary">
                <h4><i class="fas fa-user-circle"></i> Registration Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['name']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Application No</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['reg_number']) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['phone']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['email']) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Event</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['event'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Event Category</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['event_category'] ?? '') ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Game Category</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['game_category'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Game</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($registration['game'] ?? '') ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Registration Type</label>
                    <input type="text" class="form-control" value="<?= ucfirst($registration['type'] ?? '') ?>" readonly>
                </div>
                <?php if (isset($registration['type']) && $registration['type'] === 'group' && !empty($group_players)): ?>
                <div class="form-group">
                    <label>Team Members</label>
                    <div class="team-members">
                        <?php foreach($group_players as $index => $player): ?>
                        <div class="team-member">
                            <strong><?= $index + 1 ?>.</strong> 
                            <?= htmlspecialchars($player['name'] ?? '') ?> - 
                            <?= htmlspecialchars($player['reg_number'] ?? '') ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="payment-amount">
                <h4>Total Amount: ₹<?= number_format($amount, 2) ?></h4>
            </div>
            <form method="post" action="">
                <div class="payment-methods">
                    <h4><i class="fas fa-credit-card"></i> Payment Details</h4>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="pay_method" id="payMethod" class="form-control" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="upi">UPI Payment</option>
                            <option value="netbanking">Net Banking</option>
                        </select>
                    </div>
                    <div id="cardFields" class="payment-details">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Cardholder Name</label>
                                    <input type="text" name="card_name" class="form-control" placeholder="Enter cardholder name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Card Number</label>
                                    <input type="text" name="card_number" class="form-control" placeholder="Enter card number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>CVV</label>
                                    <input type="password" name="card_cvv" class="form-control" placeholder="CVV">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="upiFields" class="payment-details">
                        <div class="form-group">
                            <label>UPI ID</label>
                            <input type="text" name="upi_id" class="form-control" placeholder="Enter UPI ID">
                        </div>
                    </div>
                    <div id="netbankingFields" class="payment-details">
                        <div class="form-group">
                            <label>Select Bank</label>
                            <select name="bank" class="form-control">
                                <option value="">-- Select Bank --</option>
                                <option value="SBI">State Bank of India</option>
                                <option value="HDFC">HDFC Bank</option>
                                <option value="ICICI">ICICI Bank</option>
                                <option value="Axis">Axis Bank</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Pay Now (₹<?= number_format($amount, 2) ?>)</button>
                <a href="events.php" class="back-button">Back to Events</a>
            </form>
        <?php elseif (!$is_udgam_event): ?>
            <h2>Confirm Registration</h2>
            <div class="payment-summary">
                <h4>Registration Details</h4>
                <p><strong>Name:</strong> <?= htmlspecialchars($registration['name'] ?? '') ?></p>
                <p><strong>Application No:</strong> <?= htmlspecialchars($registration['reg_number'] ?? '') ?></p>
                <p><strong>Mobile:</strong> <?= htmlspecialchars($registration['phone'] ?? '') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($registration['email'] ?? '') ?></p>
                <p><strong>Event:</strong> <?= htmlspecialchars($registration['event'] ?? '') ?></p>
                <p><strong>Game Category:</strong> <?= htmlspecialchars($registration['game_category'] ?? '') ?></p>
                <p><strong>Game:</strong> <?= htmlspecialchars($registration['game'] ?? '') ?></p>
                <?php if (isset($registration['type']) && $registration['type'] === 'group' && !empty($group_players)): ?>
                <div class="team-members mt-3">
                    <h5>Team Members:</h5>
                    <?php foreach($group_players as $index => $player): ?>
                    <p><strong><?= $index + 1 ?>.</strong> <?= htmlspecialchars($player['name'] ?? '') ?> - <?= htmlspecialchars($player['reg_number'] ?? '') ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="alert alert-info">
                <p>No payment required for this event.</p>
            </div>
            <form method="post" action="">
                <button type="submit" class="btn btn-primary">Complete Registration</button>
                <a href="events.php" class="back-button">Back to Events</a>
            </form>
        <?php endif; ?>
    </div>
    <?php if (isset($_SESSION['payment_success']) && $_SESSION['payment_success']): ?>
    <div class="printable-form">
        <div class="receipt-container">
            <div class="watermark">SRM AP</div>
            <div class="receipt-header">
                <h2>SRM University AP</h2>
                <h3>Sports Event Registration Receipt</h3>
                <p>Official Payment Confirmation</p>
            </div>
            <div class="transaction-details">
                <div class="transaction-id">
                    Transaction ID: <?= htmlspecialchars($_SESSION['payment_details']['transaction_id'] ?? '') ?>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Status:</span>
                    <span class="receipt-value"><span class="status-badge">Paid</span></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Date:</span>
                    <span class="receipt-value"><?= isset($_SESSION['payment_details']['payment_date']) ? date('d/m/Y H:i:s', strtotime($_SESSION['payment_details']['payment_date'])) : date('d/m/Y H:i:s') ?></span>
                </div>
            </div>
            <div class="payment-amount">
                Amount Paid: ₹<?= number_format($_SESSION['payment_details']['amount'] ?? 0, 2) ?>
            </div>
            <div class="receipt-body">
                <div class="receipt-section">
                    <h4>Registration Details</h4>
                    <div class="receipt-row">
                        <span class="receipt-label">Name:</span>
                        <span class="receipt-value"><?= htmlspecialchars($registration['name'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Application No:</span>
                        <span class="receipt-value"><?= htmlspecialchars($registration['reg_number'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Mobile:</span>
                        <span class="receipt-value"><?= htmlspecialchars($registration['phone'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Email:</span>
                        <span class="receipt-value"><?= htmlspecialchars($registration['email'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Event:</span>
                        <span class="receipt-value"><?= htmlspecialchars($registration['event'] ?? '') ?></span>
                    </div>
                </div>
                <div class="section-gap"></div> 
                <div class="receipt-section">
                    <h4>Game Details</h4>
                    <div class="receipt-row">
                        <span class="receipt-label">Game Category:</span>
                        <span class="receipt-value"><?= htmlspecialchars($registration['game_category'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Game:</span>
                        <span class="receipt-value"><?= htmlspecialchars($registration['game'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Registration Type:</span>
                        <span class="receipt-value"><?= ucfirst($registration['type'] ?? '') ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Payment Method:</span>
                        <span class="receipt-value"><?= ucfirst($_SESSION['payment_details']['method'] ?? '') ?></span>
                    </div>
                </div>
                
                <?php if (isset($registration['type']) && $registration['type'] === 'group' && !empty($group_players)): ?>
                <div class="receipt-section" style="flex: 1 1 100%;">
                    <h4>Team Members</h4>
                    <table class="team-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Registration Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($group_players as $index => $player): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($player['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($player['reg_number'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="receipt-footer">
                <p>This is a computer generated receipt. No signature required. No refund possible</p>
                <p>For any queries, please contact: sportsevent.helpdesk@srmap.edu.in</p>
                <p>SRM University AP, Andhra Pradesh - 522502</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <footer class="no-print">
        <p>&copy; <?= date('Y') ?> SRM University. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = document.getElementById('payMethod');
            const cardFields = document.getElementById('cardFields');
            const upiFields = document.getElementById('upiFields');
            const netbankingFields = document.getElementById('netbankingFields');
            if (paymentMethod && cardFields && upiFields && netbankingFields) {
                cardFields.style.display = 'none';
                upiFields.style.display = 'none';
                netbankingFields.style.display = 'none';
                paymentMethod.addEventListener('change', function() {
                    cardFields.style.display = 'none';
                    upiFields.style.display = 'none';
                    netbankingFields.style.display = 'none';
                    document.querySelectorAll('#cardFields input, #upiFields input, #netbankingFields select').forEach(el => {
                        el.removeAttribute('required');
                    });
                    switch(this.value) {
                        case 'card':
                            cardFields.style.display = 'block';
                            document.querySelectorAll('#cardFields input').forEach(el => {
                                el.setAttribute('required', 'required');
                            });
                            break;
                        case 'upi':
                            upiFields.style.display = 'block';
                            document.querySelector('#upiFields input').setAttribute('required', 'required');
                            break;
                        case 'netbanking':
                            netbankingFields.style.display = 'block';
                            document.querySelector('#netbankingFields select').setAttribute('required', 'required');
                            break;
                    }
                });
                if (paymentMethod.value) {
                    paymentMethod.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
</body>
</html>