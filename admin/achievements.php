<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.html");
    exit();
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Achievement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_achievement'])) {
    $sport = $_POST['sport'];
    $image_url = $_POST['image_url'];
    $achievement = $_POST['achievement'];

    $stmt = $conn->prepare("INSERT INTO achievements (sport, image_url, achievement) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sport, $image_url, $achievement);
    
    if ($stmt->execute()) {
        echo "<script>alert('Achievement added successfully!'); window.location.href='achievements.php';</script>";
    } else {
        echo "<script>alert('Error adding achievement!');</script>";
    }
    $stmt->close();
}

// Delete Achievement
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM achievements WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Achievement deleted successfully!'); window.location.href='achievements.php';</script>";
}

// Edit Achievement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_achievement'])) {
    $edit_id = $_POST['edit_id'];
    $edit_sport = $_POST['sport'];
    $edit_image_url = $_POST['image_url'];
    $edit_achievement = $_POST['achievement'];

    $stmt = $conn->prepare("UPDATE achievements SET sport=?, image_url=?, achievement=? WHERE id=?");
    $stmt->bind_param("sssi", $edit_sport, $edit_image_url, $edit_achievement, $edit_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Achievement updated successfully!'); window.location.href='achievements.php';</script>";
    } else {
        echo "<script>alert('Error updating achievement!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Achievements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f9;
        }
        h2 { text-align: center; }
        form {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: green;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
            border-radius: 5px;
        }
        button:hover { background: darkgreen; }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th { background: #484622; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .action-container { display: flex; gap: 8px; }
        .action-btn {
            padding: 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            display: inline-block;
            flex: 1;
            text-align: center;
            font-size: 0.9em;
        }
        .edit-btn { background: #007BFF; }
        .delete-btn { background: red; }
        .edit-btn:hover { background: rgb(20, 75, 141); }
        .delete-btn:hover { background: darkred; }
        .edit-form { display: none; }
    </style>
</head>
<body>

    <h2 id="form-title">Add Achievement</h2>
    <form method="POST" action="" id="achievementForm">
        <input type="hidden" name="edit_id" id="edit_id">
        
        <label>Sport:</label>
        <select name="sport" id="sport" required>
            <option value="Cricket">Cricket</option>
            <option value="Basketball">Basketball</option>
            <option value="Lawn Tennis">Lawn Tennis</option>
            <option value="Badminton">Badminton</option>
            <option value="Kabaddi">Kabaddi</option>
            <option value="Volleyball">Volleyball</option>
            <option value="Running">Running</option>
            <option value="Football">Football</option>
            <option value="Rifle Shooting">Rifle Shooting</option>
            <option value="Archery">Archery</option>
        </select>

        <label>Image URL:</label>
        <input type="text" name="image_url" id="image_url" required>

        <label>Achievement:</label>
        <textarea name="achievement" id="achievement" required></textarea>

        <button type="submit" name="add_achievement" id="submitButton">Submit</button>
    </form>

    <h2>Existing Achievements</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Sport</th>
            <th>Image</th>
            <th>Achievement</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM achievements");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['sport']}</td>
                <td><img src='{$row['image_url']}' width='80' height='80' style='border-radius:5px;'></td>
                <td>{$row['achievement']}</td>
                <td>
                    <div class='action-container'>
                        <a href='?delete_id={$row['id']}' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        <button onclick='editAchievement({$row['id']}, \"{$row['sport']}\", \"{$row['image_url']}\", \"{$row['achievement']}\")' class='action-btn edit-btn'>Edit</button>
                    </div>
                </td>
            </tr>";
        }
        ?>
    </table>

    <script>
        function editAchievement(id, sport, imageUrl, achievement) {
            document.getElementById("form-title").innerText = "Edit Achievement";
            document.getElementById("edit_id").value = id;
            document.getElementById("sport").value = sport;
            document.getElementById("image_url").value = imageUrl;
            document.getElementById("achievement").value = achievement;
            document.getElementById("submitButton").name = "edit_achievement";
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
