-- Use the sports database
USE sports;

-- Create the admin table
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL
);

-- Create the students table (Admin manages student details)
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(255)
);

-- Create the achievements table
CREATE TABLE achievements (
    achievement_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    event_id INT,
    achievement_name VARCHAR(255),
    achievement_date DATE,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id)
);

-- Create the announcements table
CREATE TABLE announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    announcement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the event table
CREATE TABLE event (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(255),
    description TEXT
);

-- Create the game_prices table
CREATE TABLE game_prices (
    game_id INT AUTO_INCREMENT PRIMARY KEY,
    game_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);


-------------------

admin table

CREATE TABLE admin (
    admin_id INT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);


INSERT INTO admin (admin_id, username, password, email) VALUES
(1, 'admin1', '12345', 'sports@srmap.edu.in'),
(2, 'admin2', '98765', 'sportevents@srmap.edu.in');



--------------------



CREATE TABLE admin_login_history (
    history_id INT PRIMARY KEY,
    admin_id INT,
    username VARCHAR(50) NOT NULL,
    login_time DATETIME,
    logout_time DATETIME,
    browser VARCHAR(100),
    ip_address VARCHAR(50),
    login_status VARCHAR(20),
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
);


--------------------


game_prices table

-- Use the sports database
CREATE TABLE game_prices (
    id INT PRIMARY KEY,
    game_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    event VARCHAR(100) NOT NULL,
    type VARCHAR(20) NOT NULL
);

INSERT INTO game_prices (id, game_name, price, event, type) VALUES
(1, 'chess', 200.00, 'Udgam', 'single'),
(2, 'carroms', 250.00, 'Udgam', 'single'),
(3, 'badminton', 250.00, 'Udgam', 'single'),
(4, 'table tennis', 250.00, 'Udgam', 'single'),
(5, 'basketball', 800.00, 'Udgam', 'group'),
(6, 'cricket', 1200.00, 'Udgam', 'group'),
(7, 'football', 1200.00, 'Udgam', 'group'),
(8, 'kabaddi', 700.00, 'Udgam', 'group'),
(9, 'volleyball', 800.00, 'Udgam', 'group'),
(10, 'tennis', 250.00, 'Udgam', 'single'),
(11, 'badminton', 500.00, 'Udgam', 'group'),
(12, 'carroms', 500.00, 'Udgam', 'group');



------------------------


event table 


-- Use the sports database
USE sports;
-- Create the event table
CREATE TABLE admin (
    id INT PRIMARY KEY,
    name VARCHAR(255),
    tag TEXT,
    overview TEXT,
    highlights TEXT,
    image_url TEXT,
    indoor_sports TEXT,
    outdoor_sports TEXT,
    racket_sports TEXT,
    rules TEXT,
    about TEXT,
    location VARCHAR(255),
    contact VARCHAR(255),
    start_date DATE,
    end_date DATE,
    cover_image_url TEXT,
    is_payable BOOLEAN
);

INSERT INTO admin (
    id, name, tag, overview, highlights, image_url,
    indoor_sports, outdoor_sports, racket_sports, rules,
    about, location, contact, start_date, end_date,
    cover_image_url, is_payable
) VALUES (
    1,
    'Udgam',
    'Celebration of Sportsmanship and Excellence',
    'UDGAM, the prestigious national-level sports fest of SRM University - AP, has been inspiring athletes for the past three years. Scheduled from February 27 to March 2, 2025, it offers a platform for students across the country to push their limits. With a mix of athletic challenges, team sports, and cultural festivities, UDGAM embodies unity and the celebration of sportsmanship.',
    'Various Sports Competitions\nInteractive Workshops\nSports Merchandise and Events\nPrize Distribution',
    'https://srmap.edu.in/wp-content/uploads/2024/03/DSC00911-1-scaled.jpg',
    '["Chess","Carroms","Power lifting","Rope skipping","Fitness"]',
    '["Kabaddi","Cricket","Volleyball","Basketball","FootBall"]',
    '["Badminton","Table Tennis","Tennis"]',
    NULL,
    'The Sports Council of SRM University AP stands as one of the most dynamic and influential student bodies on campus. Established in 2021 under the visionary guidance of the Directorate of Sports, the council has been a trailblazer in promoting sports excellence and fostering a culture of physical fitness. We actively organize a wide array of sports events and tournaments throughout the academic year, fostering a spirit of teamwork, discipline, and camaraderie among students. Beyond organizing events, we ensure that the diverse sporting needs and aspirations of our student community are met, creating an inclusive and supportive environment for all sports enthusiasts.',
    'SRM University AP',
    'sportsevent.helpdesk@srmap.edu.in',
    '2025-02-27',
    '2025-03-02',
    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRj5QnjHPoHxCPNfyJEVRUeiF_dfk90YU0lSA&s',
    TRUE
),
(
    2,
    'ISC - Inter School Championship',
    'An exciting competition among schools in various sports',
    'The Inter School Championship (ISC) is an exciting sports competition between prominent schools, fostering a spirit of sportsmanship and healthy competition. Participating schools include SEAS, ESLA, and PSB, where students showcase their athletic skills in a wide range of events. This event offers a unique opportunity for schools to compete in a variety of sports while promoting teamwork, discipline, and overall development.',
    'Competitions between SEAS, ESLA, and PSB schools\nShowcase of young athletic talent\nInteractive sports workshops\nSports Merchandise and Booths\nPrize distribution ceremony',
    'https://srmap.edu.in/wp-content/uploads/2024/10/ISC-11-1024x683.jpg',
    '["Chess","Carroms","Fitness"]',
    '["Kabaddi","Cricket","Volleyball","Basketball","Football"]',
    '["Badminton","Tennis"]',
    NULL,
    NULL,
    'SRM University AP',
    'sportsevent.helpdesk@srmap.edu.in',
    '2025-10-09',
    '2025-11-02',
    'https://media.licdn.com/dms/image/v2/D5622AQFel7mkpFbnig/feedshare-shrink_2048_1536/feedshare-shrink_2048_1536/0/1728550610779?e=1746662400&v=beta&t=2_HvjY91jZuGLfXv_NZu3z_K5472G_IGjxAUXRrSwr8',
    FALSE
),
(
    3,
    'IHC - Inter Hostel Championship',
    'Competitive events between hostel teams',
    'The Inter Hostel Championship (IHC) is an exciting sports competition between prominent schools, fostering a spirit of sportsmanship and healthy competition. Participating hostels include Ganga, Vedavathi, Krishna, Yamuna, Godavari, Narmada, Godavari where students showcase their athletic skills in a wide range of events. This event offers a unique opportunity for hostel students to compete in a variety of sports while promoting teamwork, discipline, and overall development. The competition will heatup as hostels go head-to-head in exciting battle of skill and strategy.',
    'Competitions between Ganga, Vedavathi, Krishna, Yamuna, Godavari, Narmada, Godavari Hostels\nShowcase of young athletic talent\nInteractive sports workshops\nSports Merchandise and Booths\nPrize distribution ceremony',
    'https://srmap.edu.in/wp-content/uploads/2024/03/DSC00096-scaled.jpg',
    '["Chess","Carroms","Power Lifting","Yoga"]',
    '["Kabaddi","Cricket","Volleyball","Basketball","FootBall"]',
    '["Badminton","Tennis"]',
    NULL,
    NULL,
    'SRM University AP',
    'sportsevent.helpdesk@srmap.edu.in',
    '2025-11-21',
    '2025-12-21',
    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS76-FqajZeF04VYGni3Pn--pxBYX19u59mhg&s',
    FALSE
),
(
    4,
    'National Sports Day',
    'Celebrating the spirit of sports and fitness',
    'The Directorate of Sports at SRM University-AP organised its highly anticipated National Sports Day on March 16 and 17, 2024. The Annual Sports Day saw an impressive participation of 669 athletes. These talented athletes engaged in an intense and spirited competition, showcasing their skills and abilities in both individual and team events. It was truly a spectacle to behold, with participants and spectators alike left in awe of the incredible displays of athleticism and sportsmanship on display. The event showcased a remarkable display of skill and persistence across various competitions in many sports. The annual sports fest also culminated with a prize distribution ceremony.',
    'Various Sports Competitions\nInteractive sports workshops\nSports Merchandise and Booths\nPrize distribution ceremony',
    'https://srmap.edu.in/wp-content/uploads/2024/10/ISC-4-scaled.jpg',
    '["Chess","Carroms"]',
    '["Kabaddi","Cricket","Volleyball","Basketball","FootBall"]',
    '["Badminton","Table Tennis"]',
    NULL,
    NULL,
    'SRM University AP',
    'sportsevent.helpdesk@srmap.edu.in',
    '2025-08-29',
    '2025-08-29',
    'https://srmap.edu.in/wp-content/uploads/2021/08/SRMAP-National-Sports-Day.jpg',
    FALSE
),
(
    5,
    'PhD Scholars Sports Meet',
    'Sports league where research meets resilience',
    'The PhD Scholars Sports Meet conducted various sports from intense chess and carrom games to thrilling volleyball, badminton, table tennis matches, the day was filled with energy, teamwork, and competitive spirit. The PhD scholars show that excellence isnt just in academics but in every move, match, and game. This is hosted by SRM University AP to showcase skills and experience the thrill of competition.',
    'Various Sports Competitions among PhD scholars of all departments\nInteractive sports workshops\nSports Merchandise and Booths\nPrize distribution ceremony',
    'https://srmap.edu.in/wp-content/uploads/2024/03/DSC00983-1024x683.jpg',
    '["Chess","Carroms"]',
    '["Volleyball"]',
    '["Badminton","Table Tennis"]',
    NULL,
    NULL,
    'SRM University AP',
    'sportsevent.helpdesk@srmap.edu.in',
    '2025-11-23',
    '2025-11-24',
    'https://th.bing.com/th/id/OIP.WsAsEIbGW1WL5SaTya0_MgHaHa?rs=1&pid=ImgDetMain',
    FALSE
),
(
    6,
    'National Yoga Day',
    'Promoting physical and mental wellness through yoga sessions',
    '"Yoga is not just physical exercise. It is a union with the self, union with the universe. The universe lies outside and inside us. If we connect with ourselves, that means we are connecting with the universe”, said Vice Chancellor, SRM University-AP on the occasion of International Yoga Day Celebration held on June 21. A yoga session is held under the joint venture of the Directorate of Sports and Directorate of Student Affairs. These activities showcased not just the flexibility of the participants but also underscored the principle of unity between mind, body, and soul, fostering a sense of inner peace.',
    'Relaxing and stretching the aching thighs, tone the abdomen and strengthen the spine.\nIEnhances overall flexibility and mobility. Lessens anxiety, promotes mental tranquility.\nOm chanting and meditations have healing effects on the body and the mind.\nThe students and staff of SRM AP performed traditional and modern forms of yoga',
    'https://srmap.edu.in/wp-content/uploads/2023/06/yoga.png',
    '["Padmasana","Clap Yoga","Natya Yoga","Pranayama","Meditation"]',
    '["Surya Namaskar","Gomukhasana","Vrikshasana","Trikonasana","Vajrasana"]',
    NULL,
    NULL,
    NULL,
    'SRM University AP',
    'sportsevent.helpdesk@srmap.edu.in',
    '2025-06-21',
    '2025-06-21',
    'https://th.bing.com/th/id/OIP.9nrWikjn3BTMnGFlL4vGhQHaE8?rs=1&pid=ImgDetMain',
    FALSE
);



------------------------

announcements table

-- Use the sports database
USE sports;
-- Create the announcements table
CREATE TABLE announcements (
    id INT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);



achievements table

USE sports;

CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sport VARCHAR(100),
    image_url TEXT,
    achievement TEXT
);

INSERT INTO achievements (id, sport, image_url, achievement) VALUES
(1, 'Cricket', 'https://srmap.edu.in/wp-content/uploads/2020/03/cricket-high-resolution.jpg', 'The men''s cricket team secured 3rd place at Vignan Mahotsav 2024 with a cash prize of Rs 20,000.'),
(2, 'Basketball', 'https://srmap.edu.in/wp-content/uploads/2024/03/sports-srmap-scaled.jpg', 'Our college girls basketball team won the championship cup at the ISC, showcasing exceptional talent, dedication, and teamwork.'),
(3, 'Basketball', 'https://srmap.edu.in/wp-content/uploads/2024/03/DSC00148-1536x1024.jpg', 'The boys of SRM AP won the runners-up title at District level competitions held at Visakhapatnam.'),
(4, 'Basketball', 'https://srmap.edu.in/wp-content/uploads/2024/03/DSC00469-scaled.jpg', 'Our SRM girls received participation certificates for being part of the KLU fest.'),
(6, 'Volleyball', 'https://srmap.edu.in/wp-content/uploads/2023/01/IMG_6081-3-1024x683.jpg', 'The Men''s Volleyball team bagged the Silver Medal at the Andhra Pradesh and Telangana State-level Institutional Volleyball Championship.'),
(7, 'Volleyball', 'https://th.bing.com/th/id/OIP.p_7IJ2skWNYlNjCw3Hw8xAHaFB?rs=1&pid=ImgDetMain', 'SRM University-AP won the national-level volleyball competition held at BITS Pilani Hyderabad from February 2-5, 2023, as part of Arena 2023.'),
(8, 'Football', 'https://srmap.edu.in/wp-content/uploads/2023/04/IMG_6108-2-scaled.jpg', 'The SRM AP Men''s team received participation certificates for being part of the Amrita University tournament.'),
(9, 'Volleyball', 'https://media.licdn.com/dms/image/v2/D5622AQH-1GHNu8eahQ/feedshare-shrink_2048_1536/feedshare-shrink_2048_1536/0/1681715862647?e=1746662400&v=beta&t=Q8MetNG2JPGjSOp6lk8UisCE8l1zpLguvTeiCm6ljH4', 'Students of SRM received participation certificates for taking part in State-level Volleyball competitions.'),
(10, 'Badminton', 'https://srmap.edu.in/wp-content/uploads/2023/01/SRMAP_Sports4.jpg', 'SRM University-AP clinched victory in the Men''s South Zone Badminton Tournament, displaying outstanding skill and teamwork.'),
(11, 'Kabaddi', 'https://srmap.edu.in/wp-content/uploads/2024/03/DSC00146-1024x683.jpg', 'Our Kabaddi team won the championship during Annual Sports Day held at Gitam University, bringing pride to SRM University AP.'),
(12, 'Football', 'https://srmap.edu.in/wp-content/uploads/2020/03/football-medium-resolution.jpg', 'The SRM-AP football team reached the finals and defeated VIT-AP with a score of 3-0.'),
(13, 'Lawn Tennis', 'https://srmap.edu.in/wp-content/uploads/2020/03/news-homepage-photo-300x225.jpg', 'GKV Manikantha, a Mechanical Engineering student and passionate player, won the runner-up trophy at VITOPIA.'),
(14, 'Running', 'https://srmap.edu.in/wp-content/uploads/2024/07/Social-Media-1024x1024.jpg', 'Jyothika Sri Dandi won the Bronze medal in the 400m T20 race at the Paris Olympics, making history with her performance.'),
(15, 'Rifle Shooting', 'https://srmap.edu.in/wp-content/uploads/2022/08/bhuvitha1.png', 'Tummala Bhuvitha, a student at SRM University-AP, won 1st position and 3 gold medals in the 10m air rifle shooting competitions held in senior, junior, and youth categories at the 22nd state-level competition in Hyderabad.'),
(16, 'Archery', 'https://srmap.edu.in/wp-content/uploads/2022/01/Join-an-interactive-session-with-Ms-Tanoogna-Mallalrapu-who-won-4-Gold-Medals-in-Archery-in-the-next-session-of-THE-NEWSMAKERS.png', 'Ms. Mallarapu Tanoogna won three gold medals at the National Indoor Archery Championship-2021 and placed 8th in the National Open Ranking while representing Andhra Pradesh.'),
(17, 'Badminton', 'https://media.licdn.com/dms/image/v2/D5622AQHB1l66lTrcgQ/feedshare-shrink_2048_1536/feedshare-shrink_2048_1536/0/1730284184234?e=1746662400&v=beta&t=3mEygeVPSQlq_fO4tAdrEJotlAQLfxFbwKnOJ05ReGw', 'SRM University-AP Badminton team emerged as champions at the South Zone Inter-University Championship, securing a place in the prestigious All India Tournament.');

------------------------

students table

-- Use the sports database
USE sports;
-- Create the students table
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(255) NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    game_category VARCHAR(100) DEFAULT NULL,
    game_name VARCHAR(255) NOT NULL
);



------------------------

admin table

-- Use the sports database
USE sports;
-- Create the admin table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

------------------------

od_attendance table
CREATE TABLE od_attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_id INT NOT NULL,
    participant_type VARCHAR(50),
    attendance_date DATE,
    attendance_time TIME,
    verification_code VARCHAR(100),
    status VARCHAR(20),
    expiry_datetime DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

