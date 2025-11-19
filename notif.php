<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM announcements ORDER BY created_at ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html { height: 100%; } 
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; color: #333; min-height: 100%; display: flex; flex-direction: column; } 
        header { text-align: center; background-color: #484622; color: white; padding: 10px 0; } 
        .main-content { flex: 1; } 
        .container { width: auto; max-width: 1200px; margin: 0 auto; text-align: center; padding: 20px; } 
        h1 { font-size: 2.5rem; margin-bottom: 10px; color: #ffffff; } 
        .announcements { width: 100%; max-width: 1100px; margin: 0 auto; text-align: left; } 
        .announcement { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 15px; } 
        .announcement h3 { color: #484622; margin-top: 0; } 
        .date { color: gray; font-size: 12px; margin-bottom: 10px; } 
        footer { text-align: center; padding: 15px; background-color: #484622; color: white; margin-top: auto; } 
        .social-media { margin-top: 15px; display: flex; justify-content: center; gap: 15px; } 
        .social-media a { color: #fff; font-size: 1.5rem; transition: color 0.3s; } 
        .social-media a:hover { color: #d1a74a; } 
        .no-announcements { text-align: center; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); } 
        .message-preview { overflow: hidden;position: relative; line-height: 1.5; } 
        .message-full { display: none; line-height: 1.5; } 
        .read-more-btn { background-color: #484622; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-top: 10px; clear: both;display: block;} 
        .read-more-btn:hover { background-color: #3a3a1e; } 
        .image-gallery { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; clear: both;} 
        .image-full { display: none; }
        .image-preview img { width: 350px; height: 200px; object-fit: cover; border-radius: 4px;margin-left: 15px;margin-bottom: 10px;float: right;}
        .image-gallery img { width: 250px; height: 200px; object-fit: cover; border-radius: 4px; }
        .content-wrapper {display: flow-root;margin-bottom: 15px;}
        .clearfix::after {content: "";display: table;clear: both;}
        p {margin-top: 10px;margin-bottom: 10px;}
        .preview-text {display: -webkit-box;-webkit-line-clamp: 7;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;max-height: 210px; }
    </style>
    <script>
        function toggleReadMore(id) {
            const preview = document.getElementById('preview-' + id);
            const full = document.getElementById('full-' + id);
            const imgPreview = document.getElementById('img-preview-' + id);
            const imgFull = document.getElementById('img-full-' + id);
            const btn = document.getElementById('btn-' + id);
            if (preview.style.display !== 'none') {
                preview.style.display = 'none';
                full.style.display = 'block';
                if (imgPreview) imgPreview.style.display = 'block'; 
                if (imgFull) imgFull.style.display = 'flex';
                btn.innerText = 'Show Less';
            } else {
                preview.style.display = 'block';
                full.style.display = 'none';
                if (imgPreview) imgPreview.style.display = 'block';
                if (imgFull) imgFull.style.display = 'none';
                btn.innerText = 'Read More';
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Sports News</h1>
    </header>

    <div class="main-content">
        <div class="container">
            <div class="announcements">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $message = $row['message'];
                        $cleanMessage = str_replace('\\r\\n\\r\\n', "\n\n", $message);
                        $cleanMessage = str_replace('\\r\\n', "\n", $cleanMessage);
                        $cleanMessage = str_replace("\r\n\r\n", "\n\n", $cleanMessage);
                        $cleanMessage = str_replace("\r\n", "\n", $cleanMessage);
                        $paragraphs = explode("\n\n", $cleanMessage);
                        $formattedMessage = '';
                        foreach ($paragraphs as $paragraph) {
                            if (trim($paragraph) !== '') {
                                $formattedMessage .= "<p>" . nl2br(htmlspecialchars(trim($paragraph))) . "</p>";
                            }
                        }
                        $imageLinks = !empty($row['images']) ? explode(',', $row['images']) : [];
                        $firstImage = !empty($imageLinks) ? trim($imageLinks[0]) : '';
                        $hasMultipleImages = count($imageLinks) > 1;
                        $galleryImages = $imageLinks;
                        if ($hasMultipleImages && !empty($firstImage)) {
                            array_shift($galleryImages);
                        }
                    ?>
                        <div class="announcement">
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p class="date"><?php echo date("F d, Y h:i A", strtotime($row['created_at'])); ?></p>
                            <div class="content-wrapper">
                                <div id="preview-<?php echo $row['id']; ?>" class="message-preview">
                                    <?php if (!empty($firstImage)): ?>
                                        <div id="img-preview-<?php echo $row['id']; ?>" class="image-preview">
                                            <img src="<?php echo htmlspecialchars($firstImage); ?>" alt="Announcement image" onerror="this.src='https://via.placeholder.com/350x200?text=Image+Error';this.onerror='';">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="preview-text">
                                        <?php echo $formattedMessage; ?>
                                    </div>
                                </div>
                            </div>
                            <div id="full-<?php echo $row['id']; ?>" class="message-full">
                                <?php if (!empty($firstImage)): ?>
                                    <div class="image-preview">
                                        <img src="<?php echo htmlspecialchars($firstImage); ?>" alt="Announcement image" onerror="this.src='https://via.placeholder.com/350x200?text=Image+Error';this.onerror='';">
                                    </div>
                                <?php endif; ?>
                                <?php echo $formattedMessage; ?>
                            </div>
                            <?php if (!empty($galleryImages)): ?>
                                <div id="img-full-<?php echo $row['id']; ?>" class="image-gallery image-full">
                                    <?php 
                                    foreach ($galleryImages as $imageLink): 
                                        $imageLink = trim($imageLink);
                                        if (!empty($imageLink)):
                                    ?>
                                        <img src="<?php echo htmlspecialchars($imageLink); ?>" alt="Announcement image" onerror="this.src='https://via.placeholder.com/350x200?text=Image+Error';this.onerror='';">
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php endif; ?>
                            <button id="btn-<?php echo $row['id']; ?>" class="read-more-btn" onclick="toggleReadMore(<?php echo $row['id']; ?>)">Read More</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-announcements">
                        <p>No announcements yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 SRM University AP - All Rights Reserved</p>
        <div class="social-media">
            <a href="https://www.facebook.com/SRMUAP/" target="_blank">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="https://www.linkedin.com/school/srmuap/" target="_blank">
                <i class="fab fa-linkedin"></i>
            </a>
            <a href="https://www.instagram.com/sportscouncilsrmuap/" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://x.com/SRMUAP" target="_blank">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://www.youtube.com/c/SRMUniversityAP" target="_blank">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>