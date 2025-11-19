<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$query = "SELECT sport, achievement, image_url FROM achievements ORDER BY sport";
$result = $conn->query($query);
$achievements_by_sport = [];
while ($row = $result->fetch_assoc()) {
    $sport = $row['sport'];
    $achievements_by_sport[$sport][] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Achievements</title>
    <style>
    body{font-family:Arial,sans-serif;margin:0;padding:0;background-color:#f4f4f9;display:flex;flex-direction:column;}
    header{text-align:center;background-color:#484622;color:white;padding:0;font-size:1.5em;}
    nav{background-color:#484622;padding:10px;display:flex;justify-content:center;flex-wrap:wrap;}
    nav a{color:white;text-decoration:none;margin:8px 15px;font-weight:bold;font-size:1.1em;transition:0.1s;}
    nav a:hover{text-decoration:underline;color:orange;}
    .container{padding:30px;max-width:1000px;margin:0 auto;flex:1;}
    .sport-container{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 4px rgba(0,0,0,0.1);margin-bottom:20px;}
    .sport-container h2{text-align:center;color:#484622;border-bottom:3px solid orange;display:inline-block;padding-bottom:5px;margin-bottom:10px;font-size:1.8em;}
    .achievement-group{display:flex;justify-content:flex-start;flex-wrap:wrap;gap:20px;}
    .achievement{background:#ffffff;padding:15px;border-radius:10px;text-align:center;box-shadow:0 2px 4px rgba(0,0,0,0.1);width:250px;transition:transform 0.3s ease-in-out;}
    .achievement:hover{transform:scale(1.05);}
    .achievement img{max-width:100%;height:180px;border-radius:8px;object-fit:cover;}
    .achievement-caption{margin-top:10px;font-size:1.1em;color:#554e4e;font-weight:bold;}
    .single-achievement{display:flex;flex-direction:row;align-items:center;gap:20px;padding:15px;background:#ffffff;border-radius:10px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
    .single-achievement img{max-width:250px;height:180px;border-radius:8px;object-fit:cover;}
    .single-achievement .achievement-caption{text-align:left;margin:0;font-size:1.2em;}
    @media screen and (max-width:1024px){.achievement{width:45%;}}
    @media screen and (max-width:768px){
        nav{flex-direction:column;text-align:center;}
        nav a{display:block;padding:5px 0;}
        .achievement{width:100%;}
        .single-achievement{flex-direction:column;}
        .single-achievement img{max-width:100%;}
        .single-achievement .achievement-caption{text-align:center;margin-top:10px;}
    }
    .social-media{margin-top:15px;display:flex;justify-content:center;gap:15px;}
    .social-media a{color:#fff;font-size:1.5rem;transition:color 0.3s;}
    .social-media a:hover{color:#d1a74a;}
    footer{text-align:center;padding:15px;background-color:#484622;color:white;font-size:1em;margin-top:auto;}
    .page-wrapper{display:flex;flex-direction:column;min-height:100vh;}
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header>
            <h1>Our Achievements</h1>
        </header>
        <nav>
            <?php foreach ($achievements_by_sport as $sport => $achievements): ?>
                <a href="#<?= strtolower(str_replace(' ', '_', $sport)) ?>"><?= htmlspecialchars($sport) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="container">
            <?php if (!empty($achievements_by_sport)): ?>
                <?php foreach ($achievements_by_sport as $sport => $achievements): ?>
                    <div class="sport-container" id="<?= strtolower(str_replace(' ', '_', $sport)) ?>">
                        <h2><?= htmlspecialchars($sport) ?></h2>
                        <?php if (count($achievements) === 1): ?>
                            <div class="single-achievement">
                                <img src="<?= htmlspecialchars($achievements[0]['image_url'] ?? 'default.jpg') ?>" alt="Achievement Image">
                                <div class="achievement-caption"><?= htmlspecialchars($achievements[0]['achievement']) ?></div>
                            </div>
                        <?php else: ?>
                            <div class="achievement-group">
                                <?php foreach ($achievements as $achievement): ?>
                                    <div class="achievement">
                                        <img src="<?= htmlspecialchars($achievement['image_url'] ?? 'default.jpg') ?>" alt="Achievement Image">
                                        <div class="achievement-caption"><?= htmlspecialchars($achievement['achievement']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No achievements found.</p>
            <?php endif; ?>
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
    </div>
</body>
</html>