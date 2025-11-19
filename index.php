<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports SRM University AP</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <img src="https://srmap.edu.in/file/2019/12/White.png" alt="SRM Logo" class="logo">
        <nav>
            <a href="ourteam.html">Our Team</a>
            <a href="viewachievements.php">Achievements</a>
            <a href="events.php">Events</a>
            <a href="registration.php">Registration</a>
            <a href="notif.php">News</a>
            <a href="contactus.html">Contact Us</a>
        </nav>
    </header>
    <div class="slider">
        <?php
        $slides = [
            ["https://srmap.edu.in/wp-content/uploads/2023/04/udgam-web1-1-scaled.jpg", "#1 Sports"],
            ["https://srmap.edu.in/wp-content/uploads/2023/02/basketball-srmap-3.jpg", "#2 Basketball Court"],
            ["https://srmap.edu.in/wp-content/uploads/2023/04/srm-UDGAM-2023.jpg", "#3 Football Ground"],
            ["https://srmap.edu.in/wp-content/uploads/2022/05/campus3.jpg", "#4 Volleyball Ground"],
            ["https://srmap.edu.in/wp-content/uploads/2023/04/DSC06676-1-1-scaled.jpg", "#5 Athletic Track"],
            ["https://srmap.edu.in/wp-content/uploads/2023/04/IMG_1423-1-scaled.jpg", "#6 Tennis Court"],
            ["https://srmap.edu.in/wp-content/uploads/2023/04/IMG_3593-1-scaled.jpg", "#7 Cricket Ground"],
            ["https://srmap.edu.in/wp-content/uploads/2024/06/gymnasium.png", "#8 Gym"],
            ["https://srmap.edu.in/wp-content/uploads/2024/03/IMG_7669-768x512.jpg", "#9 Kabaddi"],
            ["https://srmap.edu.in/wp-content/uploads/2024/03/WhatsApp-Image-2024-02-29-at-17.31.58.jpeg", "#10 Chess"]
        ];
        foreach ($slides as $slide) {
            echo '<div class="slide">
                    <img src="' . $slide[0] . '" alt="' . $slide[1] . '">
                    <div class="caption">' . $slide[1] . '</div>
                  </div>';
        }
        ?>
    </div>
    <div class="about-section"><br>
    <h2 class="about-heading">About Us</h2>
    <div class="about-content">
        <div class="about-text">
            <p>Students are encouraged to participate in sports at national and international meets. Student clubs for different sports have faculty members designated to guide students. These expert trainers identify special skills, provide intensive training, and help students achieve their sports goals.</p>
            <p>Further to providing grounds and facilities for students, SRM AP - Andhra Pradesh regularly hosts local, state, and international tournaments on campus.</p>
        </div>
        <div class="notification-container">
            <h3 class="notification-title">Upcoming Event</h3>
            <p class="notification-text">Get ready for <strong>National Yoga Day - which promotes physical and mental wellness through yoga sessions on June 21, 2025!</strong> Join us for enhancing flexibility of body, doing meditation much more.</p>
            <p class="notification-text">Stay tuned! Don't miss out!</p>
        </div>
    </div><br>
    </div>
    <div class="facility-section"><br><br>
        <h2 class="facility-heading">Our Facilities</h2><br>
        <div class="facilities">
            <div class="facility"><br><br>
                <h3>Indoor Facilities</h3>
                <ul>
                    <li>Carrom</li>
                    <li>Chess</li>
                    <li>Fitness</li><br>
                    <h3>Gym</h3>
            </div>
            <div class="facility"><br><br>
                <h3>Racquet Games</h3>
                <ul>
                    <li>Tennis</li>
                    <li>Badminton</li>
                    <li>Table Tennis</li>
                </ul><br>
                <h3>Yoga and Meditation</h3>
            </div>
            <div class="facility"><br><br>
                <h3>Outdoor Games</h3>
                <ul>
                    <li>Volleyball</li>
                    <li>Football</li>
                    <li>Kabaddi</li>
                    <li>Kho-kho</li>
                    <li>Cricket</li>
                    <li>Basketball</li>
                </ul>
            </div>
            <div class="facility"><br><br>
            <h3>Athletics</h3>
                <ul>
                    <li>Long-jump</li>
                    <li>Relay</li>
                    <li>Rope Skipping</li>
                    <li>Discus Throw</li>
                    <li>Running Race</li>
                    <li>Shot put</li>
                    <li>Javelin Throw</li>
                    <li>Tug of War</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mind-soul-box">
        <div class="mind-soul-content">
            <div class="mind-soul-left">
                <br><br><h2>Mind &<br> Soul Training</h2><br><br>
            </div>            
            <div class="mind-soul-right">
                <br><br><p>Students are trained in games of their choice and taught relevant Yogasanas and Pranayamas that focus on uplifting the body, soul, mind, and spirit, to train our students to achieve their maximum efficacy. Activities in this unique course also inculcate human bonding and enhance team-building and interpersonal skills.</p><br><br>
            </div>
        </div>
    </div>
    <div class="student-champions-section">
        <h2 class="student-heading">Our Student Champions</h2>
        <div class="student-champions">
            <div class="champion"><br>
                <img src="https://media.licdn.com/dms/image/v2/D5622AQGYrKJ7lvGKvQ/feedshare-shrink_1280/feedshare-shrink_1280/0/1728503425986?e=1746662400&v=beta&t=Vr97zgOhYTDOIe1aom6qCFLLHx0cFOiSdO4w2UYHZIE" alt="Student 1">
                <h3>M. Sireesha</h3><br>
                <p>Won a bronze medal at the 4th Indian Open U-23 Athletics Competition 2024. Her remarkable performance in the 400-meter dash, finishing in just 54.63 seconds.</p>
            </div>
            <div class="champion"><br>
                <img src="https://media.licdn.com/dms/image/v2/D4E22AQFUrD5T_4Q-9Q/feedshare-shrink_2048_1536/B4EZRVFBIoGcAo-/0/1736594170874?e=1746662400&v=beta&t=wG_8xjUhHMYHfsbsjNgenGduKhYeUmcwQL-fdGFm2AM" alt="Student 2">
                <h3>J. Deepthi</h3><br>
                <p>Bronze Medalist in 400 Meter Para Olympics in Paris. Has been honoured with the prestigious Arjuna Award.</p>
            </div>
            <div class="champion"><br>
                <img src="https://srmap.edu.in/wp-content/uploads/2024/07/Social-Media-1024x1024.jpg" alt="Student 3">
                <h3>D. Jyothika Sri</h3><br>
                <p>Represented India at the Paris Olympics 2024 as part of the women's 4x400m relay team.</p>
            </div>
        </div>
    </div>
    <div class="videos-section">
        <h2 class="videos-heading">Our Videos</h2>
        <div class="videos-container">
            <div class="video-item">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/07TxJyVyh8M" allowfullscreen></iframe>
            </div>
            <div class="video-item">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/ieqKrjK00r8" allowfullscreen></iframe>
            </div>
            <div class="video-item">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/M3TCFDpxvZU" allowfullscreen></iframe>
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
    <script src="script.js"></script>
</body>
</html>