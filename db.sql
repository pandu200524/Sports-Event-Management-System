CREATE TABLE registration (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg_number VARCHAR(50) NOT NULL UNIQUE,  
    password VARCHAR(255) NOT NULL          
);



CREATE TABLE singleplayer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    reg_number VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(100),
    section VARCHAR(10),
    event VARCHAR(100),
    event_category VARCHAR(50),
    game_category VARCHAR(50),
    game VARCHAR(100),
    payment_status VARCHAR(20),
    amount DECIMAL(10,2),
    registration_date DATE
);
ALTER TABLE singleplayer
ADD COLUMN transaction_id VARCHAR(100) DEFAULT NULL,
ADD COLUMN payment_date DATETIME DEFAULT NULL;
INSERT INTO singleplayer (id, name, reg_number, email, phone, department, section, event, event_category, game_category, game, payment_status, amount, registration_date, transaction_id, payment_date) VALUES
('1', 'Vaishnavi K', 'AP22110010126', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'CSE', 'A', 'Udgam', 'Indoor', 'Carroms', NULL, 'paid', 250.00, '2025-04-12', 'UDGAM1744476095', '2025-04-12 18:41:35'),
('2', 'Ram Pradeep', 'AP22110010100', 'kraam@srmap.edu.in', '6301117919', 'ECE', 'A', 'Udgam', 'Indoor', 'Carroms', NULL, 'paid', 500.00, '2025-04-12', 'UDGAM1744477335', '2025-04-12 19:02:15'),
('3', 'Bandhavi K', 'AP22110010079', 'bandhavi_kanyadhara@srmap.edu.in', '9553728683', 'CSE', 'B', 'Udgam', 'Indoor', 'Chess', NULL, 'paid', 200.00, '2025-04-12', 'UDGAM1744475427', '2025-04-12 18:30:27'),
('4', 'Swathi N', 'AP22110010084', 'swathi_nagalla@srmap.edu.in', '8074126426', 'CSE', 'C', 'Udgam', 'Indoor', 'Chess', NULL, 'paid', 250.00, '2025-04-12', 'UDGAM1744476214', '2025-04-12 18:43:34'),
('5', 'Harshit', 'AP22110010076', 'harshit@srmap.edu.in', '9640867374', 'EEE', 'A', 'Udgam', 'Indoor', 'Carroms', NULL, 'paid', 250.00, '2025-04-12', 'UDGAM1744476069', '2025-04-12 18:41:09'),
('6', 'Hema', 'AP22110010080', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'MECH', 'B', 'Udgam', 'Indoor', 'Chess', NULL, 'paid', 500.00, '2025-04-12', 'UDGAM1744477596', '2025-04-12 19:06:36'),
('7', 'Harshit', 'AP22110010076', 'harshit@srmap.edu.in', '9640867374', 'EEE', 'A', 'Udgam', 'RacquetGames', 'Tennis', NULL, 'pending', 500.00, '2025-04-12', NULL, '2025-04-12 18:50:50'),
('8', 'Vaishnavi K', 'AP22110010126', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'CSE', 'A', 'Udgam', 'RacquetGames', 'Badminton', NULL, 'pending', 500.00, '2025-04-12', NULL, '2025-04-12 18:55:00'),
('9', 'Ram Pradeep', 'AP22110010100', 'kraam@srmap.edu.in', '6301117919', 'ECE', 'A', 'Udgam', 'RacquetGames', 'Table Tennis', NULL, 'pending', 500.00, '2025-04-12', NULL, '2025-04-12 18:59:40'),
('10', 'Swathi N', 'AP22110010084', 'swathi_nagalla@srmap.edu.in', '8074126426', 'CSE', 'C', 'Udgam', 'RacquetGames', 'Tennis', NULL, 'pending', 500.00, '2025-04-12', NULL, '2025-04-12 19:10:30'),
('11', 'Hema', 'AP22110010080', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'MECH', 'B', 'Udgam', 'RacquetGames', 'Badminton', NULL, 'pending', 500.00, '2025-04-12', NULL, '2025-04-12 18:50:20'),
('12', 'Venkat K', 'AP23110010886', 'venkat@srmap.edu.in', '8247786284', 'ECE', 'B', 'Udgam', 'RacquetGames', 'Table Tennis', NULL, 'paid', 500.00, '2025-04-12', 'UDGAM1744476562', '2025-04-12 18:59:29'),
('13', 'Venkat K', 'AP23110010886', 'venkat@srmap.edu.in', '8247786284', 'ECE', 'B', 'Udgam', 'Athletics', 'Relay', NULL, 'pending', 500.00, '2025-04-12', NULL, '2025-04-12 18:19:29'),
('14', 'Ram Pradeep', 'AP22110010100', 'kraam@srmap.edu.in', '6301117919', 'ECE', 'A', 'Udgam', 'Yoga', 'Yoga', NULL, 'pending', 500.00, '2025-04-12', NULL, '2025-04-12 18:40:50'),
('15', 'Hema', 'AP22110010080', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'EEE', 'B', 'Udgam', 'Gym', 'Gym', NULL, 'pending', 500.00, '2025-04-13', NULL, NULL),
('16', 'Hema', 'AP22110010080', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'ECE', 'B', 'Udgam', 'Yoga', 'Yoga', NULL, 'pending', 500.00, '2025-04-13', NULL, NULL),
('17', 'Abhigna', 'AP22110010752', 'abhigna_nerusu@srmap.edu.in', '9985253456', 'EEE', 'A', 'Udgam', 'Indoor', 'Carroms', NULL, 'paid', 250.00, '2025-04-13', 'UDGAM1744517897', '2025-04-13 06:18:17'),
('18', 'Asmita', 'APP22110010868', 'asmita_mareedu@srmap.edu.in', '9491667739', 'MECH', 'A', 'ISC', 'Indoor', 'Carroms', 'SEAS', 'not required', 0, '2025-04-13 09:50:12', NULL, NULL),
('19', 'Asmita', 'APP22110010868', 'asmita_mareedu@srmap.edu.in', '9491667739', 'MECH', 'A', 'IHC', 'RacquetGames', 'Tennis', 'Yamuna', 'not required', 0, '2025-04-13 10:09:19', NULL, NULL),
('20', 'Adithya', 'AP22110020054', 'adithya_mangam@srmap.edu.in', '9948272223', 'ECE', 'B', 'NSD', 'Indoor', 'Chess', 'NULL', 'paid', 0, '2025-04-13 10:12:00', NULL, NULL),
('21', 'Teja', 'AP24110011215', 'teja_vasantha@srmap.edu.in', '9989399374', 'CSE', 'C', 'YogaDay', 'Yoga', 'Yoga', 'NULL', 'paid', 0, '2025-04-13 10:14:48', NULL, NULL),
('22', 'Geetesh', 'AP24110010228', 'geetesh_kommineni@srmap.edu.in', '8019498338', 'EEE', 'B', 'NSD', 'Gym', 'Gym', 'NULL', 'paid', 0, '2025-04-13 10:16:22', NULL, NULL),
('23', 'Geetesh', 'AP24110010228', 'geetesh_kommineni@srmap.edu.in', '8019498338', 'EEE', 'B', 'ISC', 'Indoor', 'Carroms', 'SEAS', 'not required', 0, '2025-04-13 11:29:29', NULL, NULL),
('24', 'Teja', 'AP24110011215', 'teja_vasantha@srmap.edu.in', '9989399374', 'CSE', 'C', 'ISC', 'Indoor', 'Chess', 'SEAS', 'not required', 0, '2025-04-13 11:31:59', NULL, NULL),
('25', 'Hema', 'AP22110010080', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'CSE', 'B', 'ISC', 'Indoor', 'Chess', 'SEAS', 'not required', 0, '2025-04-13 11:33:11', NULL, NULL),
('26', 'Jyothi Sai Swaroop', 'AP22110010602', 'jyothisaiswaroop_mareedu@srmap.edu.in', '9440242460', 'CSE', 'D', 'ISC', 'RacquetGames', 'Badminton', 'SEAS', 'paid', 0, '2025-04-13 11:34:59', NULL, NULL),
('27', 'Vaishnavi K', 'AP22110010126', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'CSE', 'A', 'ISC', 'RacquetGames', 'Badminton', 'SEAS', 'not required', 0, '2025-04-13 11:35:41', NULL, NULL),
('28', 'Ram Pradeep', 'AP22110010100', 'kraam@gmail.com', '6301117919', 'ECE', 'B', 'ISC', 'RacquetGames', 'Tennis', 'SEAS', 'not required', 0, '2025-04-13 11:38:33', NULL, NULL),
('29', 'Adithya', 'AP22110020054', 'adithya_mangam@srmap.edu.in', '9948272223', 'ECE', 'B', 'ISC', 'RacquetGames', 'Table Tennis', 'SEAS', 'not required', 0, '2025-04-13 13:16:23', NULL, NULL),
('30', 'Roshan', 'AP24110010332', 'roshan_shaik@srmap.edu.in', '9573213233', 'CIVIL', 'A', 'ISC', 'RacquetGames', 'Badminton', 'SEAS', 'paid', 0, '2025-04-13 13:18:49', NULL, NULL),
('31', 'Roshan', 'AP24110010332', 'roshan_shaik@srmap.edu.in', '9573213233', 'CIVIL', 'A', 'IHC', 'RacquetGames', 'Badminton', 'Vedavathi', 'not required', 0, '2025-04-13 13:19:09', NULL, NULL),
('32', 'Bandhavi K', 'AP22110010079', 'bandhavi_kanyadhara@srmap.edu.in', '9553728683', 'CSE', 'B', 'IHC', 'Athletics', 'Relay', 'Krishna', 'not required', 0, '2025-04-13 13:21:12', NULL, NULL),
('33', 'Vaishnavi K', 'AP22110010126', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'CSE', 'A', 'IHC', 'Athletics', 'Relay', 'Godavari', 'not required', 0, '2025-04-13 13:22:12', NULL, NULL),
('34', 'Ram Pradeep', 'AP22110010100', 'kraam@gmail.com', '6301117919', 'ECE', 'B', 'IHC', 'Gym', 'Gym', 'Ganga', 'not required', 0, '2025-04-13 13:23:04', NULL, NULL),
('35', 'Joshua', 'AP24311010022', 'joshua_veerapaneni@srmap.edu.in', '9394855777', 'CSE', 'A', 'ISC', 'Athletics', 'Relay', 'ESLA', 'paid', 0, '2025-04-13 13:24:56', NULL, NULL),
('36', 'Joshua', 'AP24311010022', 'joshua_veerapaneni@srmap.edu.in', '9394855777', 'CSE', 'A', 'IHC', 'Yoga', 'Yoga', 'Ganga', 'not required', 0, '2025-04-13 13:25:34', NULL, NULL),
('37', 'Surya', 'AP2411001023', 'suya_vardhan@srmap.edu.in', '9963564516', 'EEE', 'B', 'IHC', 'RacquetGames', 'Tennis', 'Ganga', 'paid', 0, '2025-04-13 13:27:51', NULL, NULL),
('38', 'Surya', 'AP2411001023', 'suya_vardhan@srmap.edu.in', '9963564516', 'EEE', 'B', 'NSD', 'RacquetGames', 'Badminton', 'NULL', 'not required', 0, '2025-04-13 13:28:08', NULL, NULL),
('39', 'Kyathi', 'AP22110010845', 'kyathi_lebaka@srmap.edu.in', '9494687032', 'MECH', 'A', 'NSD', 'RacquetGames', 'Table Tennis', 'NULL', 'paid', 0, '2025-04-13 13:30:33', NULL, NULL),
('40', 'Kyathi', 'AP22110010845', 'kyathi_lebaka@srmap.edu.in', '9494687032', 'MECH', 'A', 'NSD', 'Indoor', 'Chess', 'NULL', 'not required', 0, '2025-04-13 13:31:12', NULL, NULL),
('41', 'Kyathi', 'AP22110010845', 'kyathi_lebaka@srmap.edu.in', '9494687032', 'MECH', 'A', 'IHC', 'Indoor', 'Carroms', 'Narmada', 'not required', 0, '2025-04-13 13:31:53', NULL, NULL);



CREATE TABLE groupplayers (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    reg_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(100),
    section VARCHAR(10),
    event VARCHAR(100),
    event_category VARCHAR(50),
    game_category VARCHAR(50),
    game VARCHAR(100),
    team_member_name VARCHAR(200),
    team_member_reg_number VARCHAR(200),
    amount DECIMAL(10,2),
    payment_status VARCHAR(20),
    group_id VARCHAR(50),
    registration_date DATE
);
ALTER TABLE groupplayers
ADD COLUMN transaction_id VARCHAR(100) DEFAULT NULL,
ADD COLUMN payment_date DATETIME DEFAULT NULL;


INSERT INTO groupplayers (
    name, reg_number, email, phone, department, section,
    event, event_category, game_category, game,
    team_member_name, team_member_reg_number,
    amount, payment_status, group_id, registration_date,
    transaction_id, payment_date
)
VALUES
('Adithya', 'AP22110020054', 'aditya_mangam@srmap.edu.in', '9948272223',
 'ECE', 'B', 'ISC', 'SEAS', 'Outdoor', 'Cricket',
 'Adithya,Jyothi Sai Swaroop ,Hemanth,Ram Pradeep,Venkat,Teja,Geetesh,Joshua,Roshan,Surya,Ravi',
 'AP22110020054,AP22110010602,AP23110010635,AP22110010100,AP23110010886,AP24110011215,AP24110010228,AP24311010022,AP24110010332,AP2411001023,AP23110011140',
 0.00, 'not required', 'GRP_67fbe4c7e141b', '2025-04-13', '', '0000-00-00 00:00:00'),

('Vaishnavi K', 'AP22110010126', 'vaishnavi_kolasani@srmap.edu.in', '6305094874',
 'CSE', 'A', 'Udgam', 'Outdoor', 'Volleyball', 'Vaishnavi ,Swathi,Bandhavi,Hema,Abhigna,Asmita',
 'AP22110010126,AP22110010084,AP22110010079,AP22110010080,AP22110010752,APP22110010868',
 800.00, 'paid', 'GRP_67fbe530a7185', '2025-04-13', 'UDGAM1744561465', '2025-04-13 18:24:25'),

('Rahul Sharma', 'AP22110010203', 'rahul_sharma@srmap.edu.in', '9876543210',
 'CSE', 'A', 'Udgam', NULL, 'Outdoor', 'Football',
 'Rahul, Ajay, Vijay, Sanjay, Nikhil, Aditya, Rohit, Suresh, Mahesh, Dinesh, Virat',
 'AP22110010203, AP22110010204, AP22110010205, AP22110010206, AP22110010207, AP22110010208, AP22110010209, AP22110010210, AP22110010211, AP22110010212, AP22110010213',
 800.00, 'paid', 'GRP_67fbe530a7186', '2025-04-13', 'UDGAM17445614653', '2025-04-13 18:26:35'),

('Priya Patel', 'AP22110010301', 'priya_patel@srmap.edu.in', '8765432109',
 'ECE', 'B', 'Udgam', NULL, 'Indoor', 'Badminton',
 'Priya, Neha', 'AP22110010301, AP22110010302',
 800.00, 'paid', 'GRP_67fbe530a7187', '2025-04-13', 'UDGAM17445614654', '2025-04-13 18:28:45'),

('Arjun Kumar', 'AP22110010401', 'arjun_kumar@srmap.edu.in', '7654321098',
 'MECH', 'C', 'Udgam', NULL, 'Racket', 'Tennis',
 'Arjun, Karan', 'AP22110010401, AP22110010402',
 800.00, 'paid', 'GRP_67fbe530a7188', '2025-04-13', 'UDGAM17445614655', '2025-04-13 18:30:15'),

('Sneha Reddy', 'AP22110010501', 'sneha_reddy@srmap.edu.in', '6543210987',
 'CSE', 'D', 'Phd Scholars Meet', NULL, 'Outdoor', 'Basketball',
 'Sneha, Divya, Akshay, Rohan, Riya, Shreya',
 'AP22110010501, AP22110010502, AP22110010503, AP22110010504, AP22110010505, AP22110010506',
 0.00, 'not required', 'GRP_67fbe530a7189', '2025-04-13', '', '0000-00-00 00:00:00'),

('Ankit Joshi', 'AP22110010601', 'ankit_joshi@srmap.edu.in', '5432109876',
 'ECE', 'A', 'Phd Scholars Meet', NULL, 'Indoor', 'Chess',
 'Ankit, Tanuj', 'AP22110010601, AP22110010621',
 0.00, 'not required', 'GRP_67fbe530a7190', '2025-04-13', '', '0000-00-00 00:00:00'),

('Karthik Rao', 'AP22110010701', 'karthik_rao@srmap.edu.in', '9632587410',
 'CSE', 'B', 'Udgam', NULL, 'Outdoor', 'Cricket',
 'Karthik, Dhruv, Hitesh, Imran, Jamal, Lokesh, Mahesh, Naveen, Omkar, Prakash, Qasim',
 'AP22110010701, AP22110010702, AP22110010703, AP22110010704, AP22110010705, AP22110010706, AP22110010707, AP22110010708, AP22110010709, AP22110010710, AP22110010711',
 800.00, 'paid', 'GRP_67fbe530a7191', '2025-04-13', 'UDGAM17445614656', '2025-04-13 19:15:22'),

('Meera Lakshmi', 'AP22110010801', 'meera_lakshmi@srmap.edu.in', '8523697410',
 'ECE', 'C', 'Udgam', NULL, 'Indoor', 'Table Tennis',
 'Meera, Shankar', 'AP22110010801, AP22110010802',
 800.00, 'paid', 'GRP_67fbe530a7192', '2025-04-13', 'UDGAM17445614657', '2025-04-13 19:18:45'),

('Tushar Reddy', 'AP22110010901', 'tushar_reddy@srmap.edu.in', '7418529630',
 'CSE', 'A', 'Udgam', NULL, 'Indoor', 'Basketball',
 'Tushar, Uday, Vimal, Waqar, Xavier',
 'AP22110010901, AP22110010902, AP22110010903, AP22110010904, AP22110010905',
 800.00, 'paid', 'GRP_67fbe530a7193', '2025-04-13', 'UDGAM17445614658', '2025-04-13 19:22:10');

CREATE TABLE group_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg_number VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_date DATE,
    payment_status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE group_payments
ADD COLUMN name VARCHAR(100) AFTER reg_number,
ADD COLUMN event VARCHAR(100) AFTER name,
ADD COLUMN event_category VARCHAR(50) AFTER event,
ADD COLUMN game VARCHAR(100) AFTER event_category,
ADD COLUMN game_category VARCHAR(50) AFTER game,
ADD COLUMN email VARCHAR(100) NOT NULL AFTER game_category,
ADD COLUMN phone VARCHAR(15) AFTER email,
ADD COLUMN registration_id VARCHAR(50) NOT NULL AFTER phone,
ADD COLUMN registration_type VARCHAR(50) AFTER registration_id;

INSERT INTO group_payments (
    id, reg_number, name, event, event_category, game, game_category,
    amount, payment_method, transaction_id, payment_date, payment_status,
    created_at, email, phone, registration_id, registration_type
) VALUES
(1, 'AP22110020054', 'Adithya', 'ISC', 'SEAS', 'Cricket', 'Outdoor',
 0.00, 'free', 'FREE1744561354', '2025-04-13', 'not required', '2025-04-13 21:52:34',
 'aditya_mangam@srmap.edu.in', '9948272223', 'GRP_67fbe530a7181', 'group'),

(2, 'AP22110010126', 'Vaishnavi K', 'Udgam', NULL, 'Volleyball', 'Outdoor',
 800.00, 'upi', 'UDGAM1744561465', '2025-04-13', 'paid', '2025-04-13 21:54:25',
 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'GRP_67fbe530a7123', 'group'),

(3, 'AP22110010203', 'Rahul Sharma', 'Udgam', NULL, 'Football', 'Outdoor',
 800.00, 'online', 'UDGAM1744561465', '2025-04-13', 'paid', '2025-04-13 18:25:00',
 'rahul_sharma@srmap.edu.in', '9876543210', 'GRP_67fbe530a7186', 'group'),

(4, 'AP22110010301', 'Priya Patel', 'Udgam', NULL, 'Badminton', 'Indoor',
 800.00, 'online', 'UDGAM1744561465', '2025-04-13', 'paid', '2025-04-13 18:27:00',
 'priya_patel@srmap.edu.in', '8765432109', 'GRP_67fbe530a7187', 'group'),

(5, 'AP22110010401', 'Arjun Kumar', 'Udgam', NULL, 'Tennis', 'Racket',
 800.00, 'online', 'UDGAM1744561465', '2025-04-13', 'paid', '2025-04-13 18:29:00',
 'arjun_kumar@srmap.edu.in', '7654321098', 'GRP_67fbe530a7188', 'group'),

(6, 'AP22110010501', 'Sneha Reddy', 'Phd Scholars Meet', 'free', 'Basketball', 'Outdoor',
 0.00, 'free', 'FREE1744561358', '2025-04-13', 'not required', '2025-04-13 18:31:00',
 'sneha_reddy@srmap.edu.in', '6543210987', 'GRP_67fbe530a7189', 'group'),

(7, 'AP22110010601', 'Ankit Joshi', 'Phd Scholars Meet', 'free', 'Chess', 'Indoor',
 0.00, 'free', 'FREE1744561363', '2025-04-13', 'not required', '2025-04-13 18:33:00',
 'ankit_joshi@srmap.edu.in', '5432109876', 'GRP_67fbe530a7190', 'group'),

(8, 'AP22110010701', 'Karthik Rao', 'Udgam', NULL, 'Cricket', 'Outdoor',
 800.00, 'online', 'UDGAM1744561465', '2025-04-13', 'paid', '2025-04-13 19:14:00',
 'karthik_rao@srmap.edu.in', '9632587410', 'GRP_67fbe530a7191', 'group'),

(9, 'AP22110010801', 'Meera Lakshmi', 'Udgam', NULL, 'Table Tennis', 'Indoor',
 800.00, 'online', 'UDGAM1744561465', '2025-04-13', 'paid', '2025-04-13 19:17:00',
 'meera_lakshmi@srmap.edu.in', '8523697410', 'GRP_67fbe530a7192', 'group'),

(10, 'AP22110010901', 'Tushar Reddy', 'Udgam', NULL, 'Basketball', 'Indoor',
 800.00, 'online', 'UDGAM1744561465', '2025-04-13', 'paid', '2025-04-13 19:21:00',
 'tushar_reddy@srmap.edu.in', '7418529630', 'GRP_67fbe530a7193', 'group');


CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT
    reg_number VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    event VARCHAR(100) NOT NULL,
    event_category VARCHAR(50),
    game VARCHAR(100) NOT NULL,
    game_category VARCHAR(50),
    registration_id VARCHAR(50) NOT NULL,
    registration_type VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_date DATE,
    payment_status VARCHAR(20),
    PRIMARY KEY (reg_number, registration_id)
);

INSERT INTO payments (
    reg_number, name, email, phone, event, event_category, game, game_category, registration_id, 
    registration_type, amount, payment_method, transaction_id, payment_date, payment_status
) VALUES
('1', 'AP22110010126', 'Vaishnavi K', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'Udgam', NULL, 'Carroms', 'Indoor', 1, 'single', 250.00, 'upi', 'UDGAM1744475225', '2025-04-12', 'paid'),
('2', 'AP22110010100', 'Ram Pradeep', 'kraam@gmail.com', '6301117919', 'Udgam', NULL, 'Carroms', 'Indoor', 2, 'single', 250.00, 'upi', 'UDGAM1744475345', '2025-04-12', 'paid'),
('3','AP22110010079', 'Bandhavi K', 'bandhavi_kanyadhara@srmap.edu.in', '9553728683', 'Udgam', NULL, 'Chess', 'Indoor', 3, 'single', 200.00, 'upi', 'UDGAM1744475427', '2025-04-12', 'paid'),
('4','AP22110010084', 'Swathi N', 'swathi_nagalla@srmap.edu.in', '8074126426', 'Udgam', NULL, 'Chess', 'Indoor', 4, 'single', 200.00, 'upi', 'UDGAM1744475510', '2025-04-12', 'paid'),
('5','AP22110010076', 'Harshit', 'harshit@gmail.com', '9640867374', 'Udgam', NULL, 'Carroms', 'Indoor', 5, 'single', 250.00, 'upi', 'UDGAM1744475710', '2025-04-12', 'paid'),
('6','AP22110010080', 'Hema', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'Udgam', NULL, 'Chess', 'Indoor', 6, 'single', 200.00, 'upi', 'UDGAM1744475807', '2025-04-12', 'paid'),
('7','AP22110010076', 'Harshit', 'harshit@gmail.com', '9640867374', 'Udgam', NULL, 'Tennis', 'RacquetGames', 5, 'single', 250.00, 'upi', 'UDGAM1744476069', '2025-04-12', 'paid'),
('8','AP22110010126', 'Vaishnavi K', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'Udgam', NULL, 'Badminton', 'RacquetGames', 1, 'single', 250.00, 'upi', 'UDGAM1744476095', '2025-04-12', 'paid'),
('9','AP22110010100', 'Ram Pradeep', 'kraam@gmail.com', '6301117919', 'Udgam', NULL, 'Table Tennis', 'RacquetGames', 2, 'single', 250.00, 'upi', 'UDGAM1744476151', '2025-04-12', 'paid'),
('10','AP22110010084', 'Swathi N', 'swathi_nagalla@srmap.edu.in', '8074126426', 'Udgam', NULL, 'Tennis', 'RacquetGames', 4, 'single', 250.00, 'upi', 'UDGAM1744476214', '2025-04-12', 'paid'),
('11','AP22110010080', 'Hema', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'Udgam', NULL, 'Badminton', 'RacquetGames', 6, 'single', 250.00, 'upi', 'UDGAM1744476350', '2025-04-12', 'paid'),
('12','AP23110010886', 'Venkat K', 'venkat@gmail.com', '8247786284', 'Udgam', NULL, 'Table Tennis', 'RacquetGames', 13, 'single', 250.00, 'upi', 'UDGAM1744476562', '2025-04-12', 'paid'),
('13','AP23110010886', 'Venkat K', 'venkat@gmail.com', '8247786284', 'Udgam', NULL, 'Relay', 'Athletics', 13, 'single', 500.00, 'upi', 'UDGAM1744477169', '2025-04-12', 'paid'),
('14','AP22110010100', 'Ram Pradeep', 'kraam@gmail.com', '6301117919', 'Udgam', NULL, 'Yoga', NULL, 2, 'single', 500.00, 'upi', 'UDGAM1744477335', '2025-04-12', 'paid'),
('15','AP22110010752', 'Abhigna', 'abhigna_nerusu@srmap.edu.in', '9985253456', 'Udgam', NULL, 'Carroms', 'Indoor', 25, 'single', 250.00, 'upi', 'UDGAM1744517897', '2025-04-13', 'paid'),
('16','APP22110010868', 'Asmita', 'asmita_mareedu@srmap.edu.in', '9491667739', 'ISC', 'SEAS', 'Carroms', 'Indoor', 26, 'single', 0.00, 'free', 'FREE1744518724', '2025-04-13', 'not required'),
('17','APP22110010868', 'Asmita', 'asmita_mareedu@srmap.edu.in', '9491667739', 'IHC', 'Yamuna', 'Tennis', 'RacquetGames', 26, 'single', 0.00, 'free', 'FREE1744519161', '2025-04-13', 'not required'),
('18','AP22110020054', 'Adithya', 'aditya_mangam@srmap.edu.in', '9948272223', 'NSD', NULL, 'Chess', 'Indoor', 28, 'single', 0.00, 'free', 'FREE1744519322', '2025-04-13', 'not required'),
('19','AP24110011215', 'Teja', 'teja_vasantha@srmap.edu.in', '9989399374', 'YogaDay', NULL, 'Yoga', NULL, 29, 'single', 0.00, 'free', 'FREE1744519492', '2025-04-13', 'not required'),
('20','AP24110010228', 'Geetesh', 'geetesh_kommineni@srmap.edu.in', '8019498338', 'NSD', NULL, 'Gym', NULL, 30, 'single', 0.00, 'free', 'FREE1744519584', '2025-04-13', 'not required'),
('21','AP24110010228', 'Geetesh', 'geetesh_kommineni@srmap.edu.in', '8019498338', 'ISC', 'SEAS', 'Carroms', 'Indoor', 30, 'single', 0.00, 'free', 'FREE1744523971', '2025-04-13', 'not required'),
('22','AP24110011215', 'Teja', 'teja_vasantha@srmap.edu.in', '9989399374', 'ISC', 'SEAS', 'Chess', 'Indoor', 29, 'single', 0.00, 'free', 'FREE1744524121', '2025-04-13', 'not required'),
('23','AP22110010080', 'Hema', 'hema_kakarlapudi@srmap.edu.in', '9346912878', 'ISC', 'SEAS', 'Chess', 'Indoor', 6, 'single', 0.00, 'free', 'FREE1744524194', '2025-04-13', 'not required'),
('24','AP22110010602', 'Jyothi Sai Swaroop', 'jyothisaiswaroop_mareedu@srmap.edu.in', '9440242460', 'ISC', 'SEAS', 'Badminton', 'RacquetGames', 34, 'single', 0.00, 'free', 'FREE1744524301', '2025-04-13', 'not required'),
('25','AP22110010126', 'Vaishnavi K', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'ISC', 'SEAS', 'Badminton', 'RacquetGames', 1, 'single', 0.00, 'free', 'FREE1744524343', '2025-04-13', 'not required'),
('26','AP22110010100', 'Ram Pradeep', 'kraam@gmail.com', '6301117919', 'ISC', 'SEAS', 'Tennis', 'RacquetGames', 'FREE1744524516', 'free', 0.00, 'free', '2', '2025-04-13', 'not required'),
('27','AP22110020054', 'Adithya', 'aditya_mangam@srmap.edu.in', '9948272223', 'ISC', 'SEAS', 'Table Tennis', 'RacquetGames', 'FREE1744530385', 'free', 0.00, 'free', '28', '2025-04-13', 'not required'),
('28','AP24110010332', 'Roshan', 'roshan_shaik@srmap.edu.in', '9573213233', 'ISC', 'SEAS', 'Badminton', 'RacquetGames', 'FREE1744530532', 'free', 0.00, 'free', '38', '2025-04-13', 'not required'),
('29','AP24110010332', 'Roshan', 'roshan_shaik@srmap.edu.in', '9573213233', 'IHC', 'Vedavathi', 'Badminton', 'RacquetGames', 'FREE1744530551', 'free', 0.00, 'free', '38', '2025-04-13', 'not required'),
('30','AP22110010079', 'Bandhavi K', 'bandhavi_kanyadhara@srmap.edu.in', '9553728683', 'IHC', 'Krishna', 'Relay', 'Athletics', 'FREE1744530674', 'free', 0.00, 'free', '3', '2025-04-13', 'not required'),
('31','AP22110010126', 'Vaishnavi', 'vaishnavi_kolasani@srmap.edu.in', '6305094874', 'IHC', 'Godavari', 'Relay', 'Athletics', 'FREE1744530734', 'free', 0.00, 'free', '1', '2025-04-13', 'not required'),
('32','AP22110010100', 'Ram Pradeep', 'kraam@gmail.com', '6301117919', 'IHC', 'Ganga', 'Gym', NULL, 'FREE1744530787', 'free', 0.00, 'free', '2', '2025-04-13', 'not required'),
('33','AP24311010022', 'Joshua', 'joshua_veerapaneni@srmap.edu.in', '9394855777', 'ISC', 'ESLA', 'Relay', 'Athletics', 'FREE1744530898', 'free', 0.00, 'free', '43', '2025-04-13', 'not required'),
('34','AP24311010022', 'Joshua', 'joshua_veerapaneni@srmap.edu.in', '9394855777', 'IHC', 'Ganga', 'Yoga', NULL, 'FREE1744530936', 'free', 0.00, 'free', '43', '2025-04-13', 'not required'),
('35','AP2411001023', 'Surya', 'suya_vardhan@srmap.edu.in', '9963564516', 'IHC', 'Ganga', 'Tennis', 'RacquetGames', 'FREE1744531073', 'free', 0.00, 'free', '45', '2025-04-13', 'not required'),
('36', 'AP2411001023', 'Surya', 'suya_vardhan@srmap.edu.in', '9963564516', 'Badminton', 'RacquetGames', 'single', NULL, 'FREE1744531089', 'free', 0.00,'free', 'FREE1744531089', '2025-04-13', 'not required'),
('37', 'AP24311010022', 'Joshua', 'joshua_veerapaneni@srmap.edu.in', '9394855777', 'Table Tennis', 'RacquetGames', 'single', NULL, 'FREE1744531150', 'free', 0.00, 'free', 'FREE1744531150', '2025-04-13', 'not required'),
('38', 'AP22110010845', 'Kyathi', 'kyathi_lebaka@srmap.edu.in', '9494687032', 'Table Tennis', 'RacquetGames', 'single', NULL, 'FREE1744531234', 'free', 0.00, 'free', 'FREE1744531234', '2025-04-13', 'not required'),
('39', 'AP22110010845', 'Kyathi', 'kyathi_lebaka@srmap.edu.in', '9494687032', 'Chess', 'Indoor', 'single', NULL 'FREE1744531252', 'free', 0.00, 'free', 'FREE1744531252', '2025-04-13', 'not required'),
('40', 'AP24110010332', 'Roshan', 'roshan_shaik@srmap.edu.in', '9573213233', 'Chess', 'Indoor', 'single', NULL, 'FREE1744531261', 'free', 0.00, 'free', 'FREE1744531261', '2025-04-13', 'not required'),
('41', 'AP22110010845', 'Kyathi', 'kyathi_lebaka@srmap.edu.in', '9494687032', 'Relay', 'Athletics', 'single', NULL, 'FREE1744531269', 'free', 0.00, 'free', 'FREE1744531269', '2025-04-13', 'not required');


CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    reg_number VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(100),
    section VARCHAR(10),
    event VARCHAR(100),
    event_category VARCHAR(50),
    game_category VARCHAR(50),
    game VARCHAR(100),
    player_type VARCHAR(20), 
    original_id INT,
    team_member_name VARCHAR(100),
    team_member_reg_number VARCHAR(50),
    amount DECIMAL(10,2),
    payment_status VARCHAR(20),
    registration_date DATE,
    event_name VARCHAR(100)
);
ALTER TABLE students
ADD COLUMN transaction_id VARCHAR(100) DEFAULT NULL,
ADD COLUMN payment_date DATE DEFAULT NULL,
DROP COLUMN event,
MODIFY COLUMN event_name VARCHAR(255) AFTER section;


INSERT INTO students ( 
    name, reg_number, email, phone, department, section, event_name,  
    event_category, game_category, game, player_type, original_id,
    amount, payment_status, registration_date, 
    transaction_id, payment_date
)  
SELECT  
    name, reg_number, email, phone, department, section, event,  
    event_category, game_category, game, 'single', id,
    amount, payment_status, registration_date, 
    transaction_id, payment_date
FROM singleplayer;

INSERT INTO students ( 
    name, reg_number, email, phone, department, section, event_name,  
    event_category, game_category, game, player_type, original_id,
    team_member_name, team_member_reg_number, amount, payment_status, 
    registration_date, transaction_id, payment_date
)  
SELECT  
    name, reg_number, email, phone, department, section, event,  
    event_category, game_category, game, 'group', id,
    NULL, NULL, amount, payment_status, 
    registration_date, transaction_id, payment_date
FROM groupplayers  
WHERE team_member_name IS NULL OR TRIM(team_member_name) = ''; 

INSERT INTO students ( 
    name, reg_number, email, phone, department, section, event_name,  
    event_category, game_category, game, player_type, original_id,
    team_member_name, team_member_reg_number, amount, payment_status, 
    registration_date, transaction_id, payment_date
)  
SELECT  
    name, reg_number, email, phone, department, section, event,  
    event_category, game_category, game, 'group', id,
    team_member_name, team_member_reg_number, amount, payment_status, 
    registration_date, transaction_id, payment_date
FROM groupplayers  
WHERE team_member_name IS NOT NULL AND TRIM(team_member_name) != ''; 

INSERT INTO students ( 
    name, reg_number, email, phone, department, event_category,
    player_type, original_id, event_name
)  
SELECT  
    name, reg_number, email, phone, branch, committee,
    'volunteer', volunteer_id, event_name
FROM volunteers;



CREATE TABLE volunteers (
    volunteer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    reg_number VARCHAR(50) NOT NULL,
    event_id INT,
    event_name VARCHAR(100),
    category VARCHAR(50)
);
ALTER TABLE volunteers 
ADD COLUMN branch VARCHAR(100) AFTER reg_number,
ADD COLUMN year VARCHAR(20) AFTER branch,
ADD COLUMN gender VARCHAR(10) AFTER year,
ADD COLUMN residence VARCHAR(20) AFTER gender,
ADD COLUMN committee VARCHAR(50) AFTER residence,
ADD COLUMN experience TEXT AFTER committee,
ADD COLUMN ideas TEXT AFTER experience,
ADD COLUMN improvements TEXT AFTER ideas;

INSERT INTO volunteers (volunteer_id, name, email, phone, reg_number, branch, year, gender, residence, committee, experience, ideas, improvements, event_id, event_name)
VALUES
('1', 'Asif Shaik', 'shaik.asef@lpu.in', '7670858105', 'AP22110010125', 'CSE - A', '3rd Year', 'Male', 'Hostler', 'Technical and Media', NULL, NULL, NULL, 1, 'Udgam'),
('2', 'Priya Mehta', 'priya.mehta@srmap.edu.in', '9876543210', 'AP22110010178', 'CSE - B', '2nd Year', 'Female', 'Day Scholar', 'Traditional Events', NULL, NULL, 'Better coordination between event heads and volunteers', 1, 'Udgam'),
('3', 'Rahul Verma', 'rahul.verma@srmap.edu.in', '8765432109', 'AP22110010245', 'ECE - A', '3rd Year', 'Male', 'Hostler', 'Informal Events', 'Coordinated fun activities', 'We can introduce gaming tournaments and stand-up comedy shows', 'Need to balance the timing of informal events to avoid clashes', 2, 'Phd Scholars Meet'),
('4', 'Sneha Reddy', 'sneha.reddy@srmap.edu.in', '7654321098', 'AP22110010320', 'MECH - A', '4th Year', 'Female', 'Hostler', 'Hospitality and Accommodation', NULL, 'Create a digital check-in/check-out system to streamline event processes', NULL, 1, 'Udgam'),
('5', 'Aditya Singh', 'aditya.singh@srmap.edu.in', '6543210987', 'AP22110010412', 'EEE - A', '2nd Year', 'Male', 'Day Scholar', 'Registration and Documentation', NULL, 'Implement QR code based registration to reduce queue times', NULL, 3, 'Inter School Championship'),
('6', 'Meera Krishnan', 'meera.krishnan@srmap.edu.in', '9876543211', 'AP22110010523', 'CSE - D', '3rd Year', 'Female', 'Hostler', 'Logistics and Infrastructure', 'Worked in event management team for college sports events', NULL, NULL, 1, 'Udgam'),
('7', 'Karthik Rao', 'karthik.rao@srmap.edu.in', '8765432108', 'AP22110010631', 'MECH - A', '4th Year', 'Male', 'Day Scholar', 'Writing and Certificates', 'Good at content writing, managed certificate distribution', 'Digital certificates with unique verification codes to avoid duplication', NULL, 2, 'Yoga Day'),
('8', 'Deepika Patel', 'deepika.patel@srmap.edu.in', '7654321097', 'AP22110010742', 'ECE - B', '2nd Year', 'Female', 'Hostler', 'Publicity', 'Active on social media, have experience in creating posters and event promotions', 'Use Instagram reels and YouTube shorts for better event reach', 'Start publicity campaign at least a month before the event', 4, 'Yoga Day'),
('9', 'Vikram Choudhary', 'vikram.choudhary@srmap.edu.in', '6543210986', 'AP22110010853', 'MECH - B', '3rd Year', 'Male', 'Day Scholar', 'Outreach', 'Good communication skills, have contacts in other colleges', 'Partner with local businesses to increase sponsors for events', NULL, 1, 'Udgam'),
('10', 'Ananya Sharma', 'ananya.sharma@srmap.edu.in', '9876543212', 'AP22110010964', 'Civil - A', '2nd Year', 'Female', 'Hostler', 'Refreshments', 'Managed refreshments in inter-departmental competitions', NULL, 'Need better waste management system for food packaging', 5, 'National Sports Day'),
('11', 'Rohan Kapoor', 'rohan.kapoor@srmap.edu.in', '8765432107', 'AP22110011075', 'CSE - B', '1st Year', 'Male', 'Hostler', 'Technical and Media', 'Proficient in video editing and live streaming. Has been involved in managing the live streaming of events', 'Implement live streaming of main events on YouTube to increase event visibility', NULL, 2, 'Inter Hostel Championship'),
('12', 'Neha Gupta', 'neha.gupta@srmap.edu.in', '7654321096', 'AP22110011186', 'ECE - B', '3rd Year', 'Female', 'Day Scholar', 'Transport', 'Coordinated transport for industrial visits', NULL, NULL, 1, 'Udgam'),
('13', 'Amit Kumar', 'amit.kumar@srmap.edu.in', '6543210985', 'AP22110011297', 'CSE - A', '2nd Year', 'Male', 'Hostler', 'Finance and BR', 'Good with numbers, managed budget for class events', 'Create a transparent digital system for expense tracking and management', 'Need more accurate budget estimation before events', 3, 'Inter School Championship'),
('14', 'Riya Patel', 'riya.patel@srmap.edu.in', '9876543213', 'AP22110011308', 'EEE - B', '1st Year', 'Female', 'Day Scholar', 'Photography', 'Passionate photographer with experience in event coverage', 'Create a dedicated photo booth with college-themed backgrounds for memorable pictures', 'Need more photographers during parallel events', 1, 'Udgam'),
('15', 'Arjun Nair', 'arjun.nair@srmap.edu.in', '8765432106', 'AP22110011419', 'CSE - C', '3rd Year', 'Male', 'Hostler', 'Website', 'Proficient in web development, created a website for student organizations', 'Implement a mobile-responsive design with real-time event updates', NULL, 2, 'Inter Hostel Championship'),
('16', 'Kavya Reddy', 'kavya.reddy@srmap.edu.in', '7654321095', 'AP22110011520', 'MECH - B', '2nd Year', 'Female', 'Day Scholar', 'Ceremonial', NULL, 'Include cultural performances during opening and closing ceremonies', 'Need better coordination between stage management and performers', 4, 'National Sports Day'),
('17', 'Vishal Singh', 'vishal.singh@srmap.edu.in', '6543210984', 'AP22110011631', 'ECE - A', '1st Year', 'Male', 'Hostler', 'Medical and Safeguard', 'Certified in first aid, volunteered with NSS health programs during past events', NULL, 'Need better emergency protocols and communication during events', 5, 'National Sports Day'),
('18', 'Divya Sharma', 'divya.sharma@srmap.edu.in', '9876543214', 'AP22110011742', 'CSE - D', '3rd Year', 'Female', 'Hostler', 'Traditional Events', 'Participated in classical dance competitions, organized inter-college traditional events', 'Host inter-college traditional art competitions', NULL, 1, 'Udgam'),
('19', 'Sanjay Kumar', 'sanjay.kumar@srmap.edu.in', '8765432105', 'AP22110011853', 'Civil - A', '2nd Year', 'Male', 'Day Scholar', 'Technical and Media', NULL, 'Create highlight reels of events for social media', 'Need better coordination between different technical teams', 3, 'Phd Scholars Meet'),
('20', 'Tanvi Patel', 'tanvi.patel@srmap.edu.in', '7654321094', 'AP22110011964', 'CSE - B', '3rd Year', 'Female', 'Day Scholar', 'Publicity', NULL, 'Collaborate with social media influencers for wider outreach', 'Start promotional campaigns earlier with consistent updates', 2, 'Phd Scholars Meet'),
('21', 'Mihir Joshi', 'mihir.joshi@srmap.edu.in', '9512348760', 'AP23110010777', 'CSE - A', '2nd Year', 'Male', 'Hostler', 'Technical and Media', 'Experience with video production and social media promotion', 'Create a dedicated event app with live updates and notifications', 'Better coordination between technical teams', 1, 'Udgam'),
('22', 'Aisha Khan', 'aisha.khan@srmap.edu.in', '8523697410', 'AP23110010888', 'ECE - C', '3rd Year', 'Female', 'Day Scholar', 'Registration and Documentation', 'Managed registration desk for department technical events', 'Implement pre-registration system with unique QR codes', 'Need better queue management system', 5, 'National Sports Day'),
('23', 'Rohit Verma', 'rohit.verma@srmap.edu.in', '7896541230', 'AP23110010999', 'MECH - B', '2nd Year', 'Male', 'Hostler', 'Logistics and Infrastructure', NULL, 'Create digital inventory tracking for equipment', NULL, 3, 'Inter School Championship'),
('24', 'Sanya Malhotra', 'sanya.malhotra@srmap.edu.in', '9874563210', 'AP23110011111', 'CSE - D', '1st Year', 'Female', 'Day Scholar', 'Publicity', 'Experience with graphic design and content creation', NULL, 'More coordination with other colleges for publicity', 2, 'Inter Hostel Championship'),
('25' 'Karan Mehra', 'karan.mehra@srmap.edu.in', '8745632109', 'AP23110011222', 'EEE - A', '2nd Year', 'Male', 'Hostler', 'Traditional Events', 'Organized cultural events during department fest', NULL, 'Need better scheduling of traditional events', 4, 'Yoga Day');


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



CREATE TABLE announcements (
    id INT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE event (
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
INSERT INTO event (
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


CREATE TABLE game_prices (
    id INT PRIMARY KEY,
    game_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    event VARCHAR(100) NOT NULL,
    type VARCHAR(20) NOT NULL
);
INSERT INTO game_prices (id, game_name, price, event, type) VALUES
(1, 'chess', 200.00, 'Udgam', 'single'),
(2, 'carroms', 200.00, 'Udgam', 'single'),
(3, 'badminton', 250.00, 'Udgam', 'single'),
(4, 'table tennis', 250.00, 'Udgam', 'single'),
(5, 'basketball', 800.00, 'Udgam', 'group'),
(6, 'cricket', 1200.00, 'Udgam', 'group'),
(7, 'football', 1200.00, 'Udgam', 'group'),
(8, 'kabaddi', 700.00, 'Udgam', 'group'),
(9, 'volleyball', 800.00, 'Udgam', 'group'),
(10, 'tennis', 250.00, 'Udgam', 'single'),
(11, 'badminton', 500.00, 'Udgam', 'group'),
(12, 'carroms', 500.00, 'Udgam', 'group'),
(13, 'power lifting', 250.00, 'Udgam', 'single'),
(14, 'rope skipping', 250.00, 'Udgam', 'single'),
(15, 'fitness', 250.00, 'Udgam', 'single'),
(16, 'running', 250.00, 'Udgam', 'single'),
(17, 'rifle shooting', 250.00, 'Udgam', 'single'),
(18, 'archery', 250.00, 'Udgam', 'single');


CREATE TABLE admin_login_history (
    history_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    username VARCHAR(50) NOT NULL,
    login_time DATETIME,
    logout_time DATETIME,
    browser VARCHAR(100),
    ip_address VARCHAR(50),
    login_status VARCHAR(20),
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
);


CREATE TABLE admin (
    admin_id INT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);
INSERT INTO admin (admin_id, username, password, email) VALUES
(1, 'admin1', '12345', 'sports@srmap.edu.in'),
(2, 'admin2', '98765', 'sportevents@srmap.edu.in');


CREATE TABLE attendance_verification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    event_name VARCHAR(255),
    category VARCHAR(50) NOT NULL, 
    verification_code VARCHAR(10) NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT 1
);


CREATE TABLE attendance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    person_id INT NOT NULL,
    reg_number VARCHAR(255),
    event_name VARCHAR(255),
    person_type VARCHAR(50) NOT NULL, 
    event_id INT NOT NULL,
    verification_code VARCHAR(10) NOT NULL,
    marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE matchups (
    id INT PRIMARY KEY,
    event VARCHAR(100),
    game VARCHAR(100),
    player1_id INT,
    player1_name VARCHAR(100),
    player1_reg VARCHAR(50),
    player1_dept VARCHAR(50),
    player2_id INT,
    player2_name VARCHAR(100),
    player2_reg VARCHAR(50),
    player2_dept VARCHAR(50),
    round INT,
    match_number INT,
    winner_id INT,
    score VARCHAR(20),
    type VARCHAR(20),
    status VARCHAR(50),
    next_match_id INT
);
ALTER TABLE matchups
ADD COLUMN winner_name VARCHAR(100) AFTER winner_id,;
UPDATE matchups
SET winner_name = CASE winner_id
    WHEN player1_id THEN player1_name
    WHEN player2_id THEN player2_name
    ELSE NULL
END;

INSERT INTO matchups (id, event, game, category, player1_id, player1_name, player1_reg, player1_dept, player2_id, player2_name, player2_reg, player2_dept, round, match_number, winner_id, winner_name, score, type, status, next_match_id)
VALUES
(1, 'IHC', 'Relay', 32, 'Bandhavi K', 'AP22110010079', 'CSE', 33, 'Vaishnavi K', 'AP22110010126', 'CSE', 1, 1, 33, 'Vaishnavi K', '1-0', 'single', 'completed', NULL),
(2, 'IHC', 'Tennis', 19, 'Asmita', 'APP22110010868', 'MECH', 37, 'Surya', 'AP2411001023', 'EEE', 1, 1, 19, 'Asmita', '9-4', 'single', 'completed', NULL),
(3, 'ISC', 'Badminton', 26, 'Jyothi Sai Swaroop', 'AP22110010602', 'CSE', 27, 'Vaishnavi K', 'AP22110010126', 'CSE', 1, 1, NULL, NULL, NULL, 'single', 'pending', 4),
(4, 'ISC', 'Badminton', 30, 'Roshan', 'AP24110010332', 'CIVIL', NULL, 'TBD (Winner of Round 1)', 2, 2, NULL, NULL, NULL, 'single', 'pending', NULL),
(5, 'ISC', 'Carroms', 18, 'Asmita', 'APP22110010868', 'MECH', 23, 'Geetesh', 'AP24110010228', 'EEE', 1, 1, NULL, NULL, NULL, 'single', 'pending', NULL),
(6, 'ISC', 'Chess', 24, 'Teja', 'AP24110011215', 'CSE', 25, 'Hema', 'AP22110010080', 'CSE', 1, 1, NULL, NULL, NULL, 'single', 'pending', NULL),
(7, 'NSD', 'Chess', 20, 'Adithya', 'AP22110020054', 'ECE', 40, 'Kyathi', 'AP22110010845', 'MECH', 1, 1, NULL, NULL, NULL, 'single', 'pending', NULL),
(8, 'Udgam', 'Badminton', 8, 'Vaishnavi K', 'AP22110010126', 'CSE', 11, 'Hema', 'AP22110010080', 'MECH', 1, 1, 8, 'Vaishnavi K', '3-0', 'single', 'completed', NULL),
(9, 'Udgam', 'Carroms', 1, 'Vaishnavi K', 'AP22110010126', 'CSE', 17, 'Abhigna', 'AP22110010752', 'EEE', 1, 1, 17, 'Abhigna', '3-1', 'single', 'completed', 11),
(10, 'Udgam', 'Carroms', 5, 'Harshit', 'AP22110010076', 'EEE', 2, 'Ram Pradeep', 'AP22110010100', 'ECE', 1, 2, 5, 'Harshit', '2-1', 'single', 'completed', 11),
(11, 'Udgam', 'Carroms', 17, 'Abhigna', 'AP22110010752', 'EEE', 5, 'Harshit', 'AP22110010076', 'EEE', 2, 3, 17, 'Abhigna', '5-2', 'single', 'completed', NULL),
(14, 'Udgam', 'Table Tennis', 9, 'Ram Pradeep', 'AP22110010100', 'ECE', 12, 'Venkat K', 'AP23110010886', 'ECE', 1, 1, NULL, NULL, NULL, 'single', 'pending', NULL),
(15, 'Udgam', 'Tennis', 10, 'Swathi N', 'AP22110010084', 'CSE', 7, 'Harshit', 'AP22110010076', 'EEE', 1, 1, NULL, NULL, NULL, 'single', 'pending', NULL),
(16, 'Udgam', 'Yoga', 16, 'Hema', 'AP22110010080', 'ECE', 14, 'Ram Pradeep', 'AP22110010100', 'ECE', 1, 1, NULL, NULL, NULL, 'single', 'pending', NULL),
(17, 'Udgam', 'Chess', 3, 'Bandhavi K', 'AP22110010079', 'CSE', 6, 'Hema', 'AP22110010080', 'MECH', 1, 1, 3, 'Bandhavi K', '3-0', 'single', 'completed', 19),
(18, 'Udgam', 'Chess', 4, 'Swathi N', 'AP22110010084', 'CSE', 42, 'Aryan Sharma', 'AP23110010456', 'CSE', 1, 2, 4, 'Swathi N', '2-1', 'single', 'completed', 19),
(19, 'Udgam', 'Chess', 3, 'Bandhavi K', 'AP22110010079', 'CSE', 4, 'Swathi N', 'AP22110010084', 'CSE', 2, 3, 4, 'Swathi N', '3-2', 'single', 'completed', NULL);
