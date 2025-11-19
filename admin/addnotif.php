<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Handle Adding an Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $message = $conn->real_escape_string($_POST['message']);
    
    // Process image links if provided
    $images = !empty($_POST['images']) ? $conn->real_escape_string($_POST['images']) : null;
    
    if (!empty($title) && !empty($message)) {
        // Insert announcement with image links
        $sql = "INSERT INTO announcements (title, message, images) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $title, $message, $images);
        
        if ($stmt->execute()) {
            echo "<script>alert('Announcement added successfully!'); window.location.href='addnotif.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Title and message are required!');</script>";
    }
}

// Handle Editing an Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $message = $conn->real_escape_string($_POST['message']);
    $images = !empty($_POST['images']) ? $conn->real_escape_string($_POST['images']) : null;
    
    // Update the announcement
    $sql = "UPDATE announcements SET title = ?, message = ?, images = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $message, $images, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Announcement updated successfully!'); window.location.href='addnotif.php';</script>";
    } else {
        echo "<script>alert('Error updating: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle Deleting an Announcement
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Delete the announcement
    $sql = "DELETE FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Announcement deleted successfully!'); window.location.href='addnotif.php';</script>";
    } else {
        echo "<script>alert('Error deleting: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch all announcements
$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; text-align: center; padding: 20px; }
        h1 { color: #484622; }
        .container { width: 55%; margin: auto; text-align: left; }
        .container1 { width: 75%; margin: auto; text-align: left; }
        .admin-panel, .announcement { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .admin-panel input, .admin-panel textarea, .admin-panel button { width: 90%; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .admin-panel button { background-color: #484622; color: white; cursor: pointer; }
        .admin-panel button:hover { background-color: #3a3a1e; }
        .announcement h3 { margin-bottom: 5px; }
        .date { color: gray; font-size: 12px; margin-bottom: 10px; }
        .actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px; }
        .actions a { text-decoration: none; padding: 8px 12px; border-radius: 5px; color: white; }
        .edit { background: #3498db; }
        .delete { background: #e74c3c; }
        .edit:hover { background: #217dbb; }
        .delete:hover { background: #c0392b; }
        .image-preview { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .image-container { position: relative; width: 150px; height: 150px; overflow: hidden; margin-bottom: 10px; }
        .image-container img { width: 100%; height: 100%; object-fit: cover; }
        .note { color: #666; font-size: 12px; margin-top: 5px; }
    </style>
</head>
<body>

<h1>Admin - Manage Notifications</h1>

<div class="container">
    <div class="admin-panel">
        <h2>Add Announcement</h2>
        <form method="post">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="message" placeholder="Message" required rows="5"></textarea>
            <textarea name="images" placeholder="Add image paths (comma separated) - Optional" rows="3"></textarea>
            <button type="submit" name="add">Post Announcement</button>
        </form>
    </div>
</div>
<div class="container1">
    <h2>All Announcements</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="announcement">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="date"><?php echo date("F d, Y h:i A", strtotime($row['created_at'])); ?></p>
                <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                
                <?php if (!empty($row['images'])): ?>
                    <div class="image-preview">
                        <?php 
                        $imageLinks = explode(',', $row['images']);
                        foreach ($imageLinks as $imageLink): 
                            $imageLink = trim($imageLink);
                            if (!empty($imageLink)):
                        ?>
                            <div class="image-container">
                                <img src="<?php echo htmlspecialchars($imageLink); ?>" alt="Announcement image" onerror="this.src='https://via.placeholder.com/150?text=Image+Error';this.onerror='';">
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="actions">
                    <a href="addnotif.php?edit=<?php echo $row['id']; ?>" class="edit">Edit</a>
                    <a href="addnotif.php?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
                </div>
            </div>

            <!-- Edit Form (Hidden until clicked) -->
            <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']): ?>
                <div class="admin-panel">
                    <h2>Edit Announcement</h2>
                    <form method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                        <textarea name="message" required rows="5"><?php echo htmlspecialchars($row['message']); ?></textarea>
                        
                        <textarea name="images" rows="3"><?php echo htmlspecialchars($row['images'] ?? ''); ?></textarea>
                        <p class="note">Update image paths (comma separated). Current images will be replaced.</p>
                        
                        <button type="submit" name="edit">Update Announcement</button>
                    </form>
                </div>
            <?php endif; ?>

        <?php endwhile; ?>
    <?php else: ?>
        <p>No announcements available.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>