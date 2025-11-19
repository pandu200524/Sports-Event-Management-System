<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$isAdmin = isset($_SESSION['admin_username']);

if ($isAdmin && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "save") {
    $game_name = $conn->real_escape_string($_POST['game_name']);
    $price = floatval($_POST['price']);
    $event_type = $conn->real_escape_string($_POST['type']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($game_name) || empty($price) || empty($event_type)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    if ($id > 0) {
        // Fixed: Here $type was used without being defined, changed to $event_type
        $sql = "UPDATE game_prices SET game_name = '$game_name', price = '$price', type = '$event_type' WHERE id = '$id'";
    } else {
        // Fixed: Same issue here with $type, changed to $event_type
        $sql = "INSERT INTO game_prices (game_name, price, type) VALUES ('$game_name', '$price', '$event_type')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Price updated successfully!'); window.location.href = 'payments.php';</script>";
    } else {
        echo "<script>alert('Error updating price: " . $conn->error . "'); window.history.back();</script>";
    }
}

if ($isAdmin && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "delete") {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM game_prices WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Price deleted successfully!'); window.location.href = 'payments.php';</script>";
    } else {
        echo "<script>alert('Error deleting price: " . $conn->error . "'); window.history.back();</script>";
    }
}

// Get single event prices
$query_single = "SELECT id, game_name, price FROM game_prices WHERE type = 'single' ORDER BY game_name";
$result_single = $conn->query($query_single);
$single_prices = [];
while ($row = $result_single->fetch_assoc()) {
    $single_prices[] = $row;
}

// Get group event prices
$query_group = "SELECT id, game_name, price FROM game_prices WHERE type = 'group' ORDER BY game_name";
$result_group = $conn->query($query_group);
$group_prices = [];
while ($row = $result_group->fetch_assoc()) {
    $group_prices[] = $row;
}

// Get all prices for editing
$query_all = "SELECT id, game_name, price, type FROM game_prices ORDER BY type, game_name";
$result_all = $conn->query($query_all);
$all_prices = [];
while ($row = $result_all->fetch_assoc()) {
    $all_prices[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; padding: 20px; }
        h2 { color: #484622; }
        .form-container {background: white;padding: 20px;border-radius: 8px;box-shadow: 0 4px 8px rgba(0,0,0,0.1);width: 40%;margin: auto;}
        .form-group {display: flex;align-items: center;margin-bottom: 15px;text-align: left;}
        .form-group label {width: 30%;font-weight: bold;padding-right: 10px;}
        .form-group input, 
        .form-group select {width: 70%;padding: 10px;border: 1px solid #ccc;border-radius: 5px;}
        button { background-color: #484622; color: white; padding: 12px 24px; border: none; cursor: pointer; border-radius: 5px; margin-top: 15px; }
        button:hover { background-color: rgb(53, 53, 31); }
        .container {max-width: 800px;margin: 20px auto;padding: 10px;background: white;border-radius: 8px;box-shadow: 0 4px 8px rgba(0,0,0,0.1);}
        table {width: 100%;border-collapse: collapse;}
        th, td {border: 1px solid #ddd;padding: 10px;text-align: left;}
        th {background-color: #484622;color: white;}
        .edit-btn {background-color: #007BFF;color: white;padding: 5px 10px;border: none;cursor: pointer;border-radius: 3px;}
        .delete-btn {background-color: #FF3B30;color: white;padding: 5px 10px;border: none;cursor: pointer;border-radius: 3px;}
        .edit-btn:hover { background-color: #0056b3; }
        .delete-btn:hover { background-color: #cc0000; }
        .price-tables { display: flex; gap: 20px; justify-content: space-between; }
        .price-table { flex: 1; }
        .section-header { background-color: #e0e0e0; padding: 10px; border-radius: 5px 5px 0 0; margin-bottom: 0; border-bottom: 2px solid #484622; }
        @media screen and (max-width: 768px) {
            .form-container { width: 90%; }
            .price-tables { flex-direction: column; }
            .form-group { flex-direction: column; align-items: flex-start; }
            .form-group label, .form-group input, .form-group select { width: 100%; }
        }
    </style>
</head>
<body>
<h2>Set Prices for Udgam</h2>
<?php if ($isAdmin): ?>
    <div class="form-container">
        <form method="POST" id="priceForm">
            <input type="hidden" name="id" id="priceId">
            <input type="hidden" name="action" value="save">
            <div class="form-group">
                <label for="game_name">Game Name:</label>
                <input type="text" name="game_name" id="game_name" required>
            </div>
            <div class="form-group">
                <label for="price">Price (₹):</label>
                <input type="number" name="price" id="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="type">Event Type:</label>
                <select name="type" id="type" required>
                    <option value="">Select Event Type</option>
                    <option value="single">Single</option>
                    <option value="group">Group</option>
                </select>
            </div>
            
            <button type="submit" id="submitButton">Set Price</button>
        </form>
    </div>
<?php endif; ?>
<div class="container">
    <h2>Udgam Event Pricing</h2>
    <div class="price-tables">
        <!-- Single Events Table -->
        <div class="price-table">
            <h3 class="section-header">Single Events</h3>
            <table>
                <tr>
                    <th>Game Name</th>
                    <th>Price (₹)</th>
                    <?php if ($isAdmin): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
                <?php if (!empty($single_prices)): ?>
                    <?php foreach ($single_prices as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['game_name']) ?></td>
                            <td>₹<?= number_format($row['price'], 2) ?></td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <button class="edit-btn" onclick="editPrice(<?= $row['id'] ?>, '<?= htmlspecialchars($row['game_name']) ?>', <?= $row['price'] ?>, 'single')">Edit</button>
                                    <button class="delete-btn" onclick="deletePrice(<?= $row['id'] ?>)">Delete</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isAdmin ? '3' : '2' ?>">No single event prices set yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>
        
        <!-- Group Events Table -->
        <div class="price-table">
            <h3 class="section-header">Group Events</h3>
            <table>
                <tr>
                    <th>Game Name</th>
                    <th>Price (₹)</th>
                    <?php if ($isAdmin): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
                <?php if (!empty($group_prices)): ?>
                    <?php foreach ($group_prices as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['game_name']) ?></td>
                            <td>₹<?= number_format($row['price'], 2) ?></td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <button class="edit-btn" onclick="editPrice(<?= $row['id'] ?>, '<?= htmlspecialchars($row['game_name']) ?>', <?= $row['price'] ?>, 'group')">Edit</button>
                                    <button class="delete-btn" onclick="deletePrice(<?= $row['id'] ?>)">Delete</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isAdmin ? '3' : '2' ?>">No group event prices set yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<script>
    function editPrice(id, gameName, price, eventType) {
        document.getElementById('priceId').value = id;
        document.getElementById('game_name').value = gameName;
        document.getElementById('price').value = price;
        document.getElementById('type').value = eventType;
        document.getElementById('submitButton').textContent = "Update Price";
        
        // Scroll to the form
        document.querySelector('.form-container').scrollIntoView({behavior: 'smooth'});
    }
    
    function deletePrice(id) {
        if (confirm("Are you sure you want to delete this price?")) {
            document.body.innerHTML += `<form method="POST" id="deleteForm"><input type="hidden" name="id" value="${id}"><input type="hidden" name="action" value="delete"></form>`;
            document.getElementById("deleteForm").submit();
        }
    }
</script>
</body>
</html>