-- Use the sports database
USE sports;

-- Create the groupplayers table (For team-based events)
CREATE TABLE groupplayers (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    player_id INT,
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (player_id) REFERENCES students(student_id)
);

-- Create the singleplayer table (For individual participation)
CREATE TABLE singleplayer (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    event_id INT,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id)
);

-- Create the registration table (Tracks event registration)
CREATE TABLE registration (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    event_id INT,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id)
);




-----------------------------------


singleplayer table

-- Use the sports database
USE sports;
-- Create the singleplayer table
CREATE TABLE singleplayer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    reg_number VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(100),
    section VARCHAR(10),
    event VARCHAR(100),
    game_category VARCHAR(50),
    game VARCHAR(100),
    event_category VARCHAR(50),
    payment_status VARCHAR(20),
    amount DECIMAL(10,2),
    registration_date DATE
);


-----------------------------------

registration table

-- Use the sports database
USE sports;
Create the registration tableCREATE TABLE registration (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg_number VARCHAR(50) NOT NULL UNIQUE,  
    password VARCHAR(255) NOT NULL          
);




-----------------------------------

groupplayers table

-- Use the sports database
USE sports;
-- Create the groupplayers table
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
    team_member_name VARCHAR(100),
    team_member_reg_number VARCHAR(20),
    amount DECIMAL(10,2),
    payment_status VARCHAR(20),
    group_id VARCHAR(50),
    registration_date DATE
);



-----------------------------------

group payments table

CREATE TABLE group_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_date DATE,
    payment_status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-----------------------------------
payments table

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_id INT NOT NULL,
    registration_type VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_date DATE,
    payment_status VARCHAR(20)
);


-----------------------------------
students table


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
    player_type VARCHAR(20), -- e.g., 'single', 'group'
    original_id INT, -- for tracking original registration reference if needed
    group_member_name VARCHAR(100),
    team_member_reg_number VARCHAR(50),
    group_id INT,
    team_leader BOOLEAN DEFAULT FALSE,
    amount DECIMAL(10,2),
    payment_status VARCHAR(20),
    registration_date DATE,
    event_id INT,
    event_name VARCHAR(100)
);

INSERT INTO students (
    name, reg_number, email, phone, department, section, event, 
    event_category, game_category, game, payment_status, amount, 
    registration_date, player_type, original_id
)
SELECT 
    name, reg_number, email, phone, department, section, event, 
    event_category, game_category, game, payment_status, amount, 
    registration_date, 'single', id
FROM singleplayer;


INSERT INTO students (
    name, reg_number, email, phone, department, section, event, 
    event_category, game_category, game, payment_status, amount, 
    registration_date, group_id, player_type, original_id
)
SELECT 
    name, reg_number, email, phone, department, section, event, 
    event_category, game_category, game, payment_status, amount, 
    registration_date, group_id, 'group_leader', id
FROM groupplayers
WHERE group_id IS NULL OR group_id = 0;


INSERT INTO students (
    name, reg_number, email, phone, department, section, event, 
    event_category, game_category, game, payment_status, amount, 
    registration_date, group_member_name, team_member_reg_number, group_id, 
    player_type, original_id
)
SELECT 
    name, reg_number, email, phone, department, section, event, 
    event_category, game_category, game, payment_status, amount, 
    registration_date, team_member_name, team_member_reg_number, group_id, 
    'group_member', id
FROM groupplayers
WHERE group_id IS NOT NULL AND group_id > 0;


INSERT INTO students (
    name, reg_number, email, phone, event_id, event_name, player_type, original_id
)
SELECT 
    name, reg_number, email, phone, event_id, event_name, 'volunteer', volunteer_id
FROM volunteers;

-----------------------------
volunteers table

CREATE TABLE volunteers (
    volunteer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    reg_number VARCHAR(50) NOT NULL,
    event_id INT,
    event_name VARCHAR(100)
);






CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reg_number VARCHAR(50) NOT NULL,
    activity_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    attendance_date DATE NOT NULL,
    time_slot VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);