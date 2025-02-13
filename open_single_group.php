<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Portal Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background-color: #f4f4f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header, footer {
            background-color: #484622;
            color: white;
            padding: 15px 0;
        }
        .container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            padding: 40px;
            margin-top: 20px;
            flex-grow: 1;
        }
        .section {
            width: 45%;
            text-align: left;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section h2 {
            color: #000000;
        }
        .button {
            display: inline-block;
            margin: 10px 0;
            padding: 15px 25px;
            font-size: 16px;
            color: white;
            background-color: #484622;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .button:hover {
            background-color: #484622;
        }
        .description {
            font-size: 18px;
            color: #333;
            line-height: 1.8;
        }
        .form-container {
            display: none;
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container input,
        .form-container select,
        .form-container button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        #eventOptions, #games {
            display: none;
        }
        .team-member {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Sports Portal</h1>
    </header>

    <div id="mainContainer" class="container">
        <div class="section">
            <h2>Single Player Game</h2>
            <p class="description">If you enjoy testing your skills and competing against yourself or others, our single-player games are perfect for you. These games not only improve your focus and self-discipline but also help you push your boundaries and achieve personal growth. Join now and take the first step towards excellence in individual sports!</p>
            <a href="javascript:void(0);" class="button" onclick="showForm('singlePlayerForm')">Register for Single Player</a>
        </div>

        <div class="section">
            <h2>Group Game</h2>
            <p class="description">For those who thrive on teamwork and collaboration, group games offer the perfect opportunity to build bonds, strategize, and achieve together. By joining a team sport, you’ll enhance your communication and leadership skills while experiencing the thrill of collective success. Sign up now and be a part of something bigger!</p>
            <a href="javascript:void(0);" class="button" onclick="showForm('groupGameForm')">Register for Group Game</a>
        </div>
    </div>

    <!-- Single Player Registration Form -->
    <div id="singlePlayerForm" class="form-container">
        <h2>Single Player Registration</h2>
        <form action="single.php" method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="reg_number" placeholder="Registration Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="number" name="phone" placeholder="Phone Number" required>
            <select name="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">Mechanical</option>
                <option value="CIVIL">Civil</option>
            </select>
            <input type="text" name="section" placeholder="Section" required>
            <select name="event" id="event" onchange="showEventOptions()" required>
                <option value="" disabled selected>Select Event</option>
                <option value="Udgam">Udgam</option>
                <option value="ISC">Interschool Championship (ISC)</option>
                <option value="IHC">Interhostel Championship (IHC)</option>
                <option value="NSD">National Sports Day</option>
                <option value="YogaDay">Yoga Day</option>
                <option value="PhDMeet">PhD Scholars Meet</option>
            </select>
            <div id="eventOptions" style="display: none;"></div>
            <select name="game_category" id="gameCategory" onchange="showGames()" required>
                <option value="" disabled selected>Select Game Category</option>
                <option value="Indoor">Indoor</option>
                <option value="Outdoor">Outdoor</option>
                <option value="RacquetGames">Racquet Games</option>
                <option value="Athletics">Athletics</option>
                <option value="Yoga">Yoga and Meditation</option>
                <option value="Gym">Gym</option>
            </select>
            <div id="games" style="display: none;"></div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <!-- Group Game Registration Form -->
    <div id="groupGameForm" class="form-container">
        <h2>Group Game Registration</h2>
        <form action="group.php" method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="reg_number" placeholder="Registration Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="number" name="phone" placeholder="Phone Number" required>
            <select name="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="CSE">CSE</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">Mechanical</option>
                <option value="CIVIL">Civil</option>
            </select>
            <input type="text" name="section" placeholder="Section" required>
            <select name="event" id="eventGroup" onchange="showEventOptionsGroup()" required>
                <option value="" disabled selected>Select Event</option>
                <option value="Udgam">Udgam</option>
                <option value="ISC">Interschool Championship (ISC)</option>
                <option value="IHC">Interhostel Championship (IHC)</option>
                <option value="NSD">National Sports Day</option>
                <option value="YogaDay">Yoga Day</option>
                <option value="PhDMeet">PhD Scholars Meet</option>
            </select>
            <div id="eventOptionsGroup" style="display: none;"></div>
            <select name="game_category" id="gameCategoryGroup" onchange="showGamesGroup()" required>
                <option value="" disabled selected>Select Game Category</option>
                <option value="Indoor">Indoor</option>
                <option value="Outdoor">Outdoor</option>
                <option value="RacquetGames">Racquet Games</option>
                <option value="Athletics">Athletics</option>
                <option value="Yoga">Yoga and Meditation</option>
                <option value="Gym">Gym</option>
            </select>
            <div id="gamesGroup" style="display: none;"></div>
            <div id="teamMembers" style="display: none;"></div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 SRM University. All rights reserved.</p>
    </footer>

    <script>
        function showForm(formId) {
            document.getElementById('mainContainer').style.display = 'none';
            document.getElementById(formId).style.display = 'block';
        }

        function showEventOptions() {
            const event = document.getElementById('event').value;
            const eventOptions = document.getElementById('eventOptions');
            let options = '';

            if (event === 'ISC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select ISC Category</option>
                        <option value="SEAS">SEAS</option>
                        <option value="PSB">PSB</option>
                        <option value="ESLA">ESLA</option>
                    </select>`;
            } else if (event === 'IHC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select Hostel</option>
                        <option value="Vedavathi">Vedavathi</option>
                        <option value="Ganga">Ganga</option>
                        <option value="Krishna">Krishna</option>
                        <option value="Yamuna">Yamuna</option>
                        <option value="Narmada">Narmada</option>
                        <option value="Kaveri">Kaveri</option>
                        <option value="Godavari">Godavari</option>
                    </select>`;
            }

            eventOptions.innerHTML = options;
            eventOptions.style.display = options ? 'block' : 'none';
        }

        function showGames() {
            const category = document.getElementById('gameCategory').value;
            const games = document.getElementById('games');
            let options = '';

            if (category === 'Indoor') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Indoor Game</option>
                        <option value="Carroms">Carroms</option>
                        <option value="Chess">Chess</option>
                        <option value="Table Tennis">Table Tennis</option>
                    </select>`;
            } else if (category === 'Outdoor') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Outdoor Game</option>
                        <option value="Volleyball">Volleyball</option>
                        <option value="Football">Football</option>
                        <option value="Kabaddi">Kabaddi</option>
                        <option value="Kho-Kho">Kho-Kho</option>
                        <option value="Cricket">Cricket</option>
                        <option value="Basketball">Basketball</option>
                    </select>`;
            } else if (category === 'RacquetGames') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Racket Game</option>
                        <option value="Tennis">Tennis</option>
                        <option value="Badminton">Badminton</option>
                        <option value="Table Tennis">Table Tennis</option>
                    </select>`;
            } else if (category === 'Athletics') {
                options = `
                    <select name="game" required>
                        <option value="" disabled selected>Select Athletics Game</option>
                        <option value="Relay">Relay</option>
                        <option value="Tug of War">Tug of War</option>
                    </select>`;
            }

            games.innerHTML = options;
            games.style.display = options ? 'block' : 'none';
        }

        function showEventOptionsGroup() {
            const event = document.getElementById('eventGroup').value;
            const eventOptionsGroup = document.getElementById('eventOptionsGroup');
            let options = '';

            if (event === 'ISC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select ISC Category</option>
                        <option value="SEAS">SEAS</option>
                        <option value="PSB">PSB</option>
                        <option value="ESLA">ESLA</option>
                    </select>`;
            } else if (event === 'IHC') {
                options = `
                    <select name="event_category" required>
                        <option value="" disabled selected>Select Hostel</option>
                        <option value="Vedavathi">Vedavathi</option>
                        <option value="Ganga">Ganga</option>
                        <option value="Krishna">Krishna</option>
                        <option value="Yamuna">Yamuna</option>
                        <option value="Narmada">Narmada</option>
                        <option value="Kaveri">Kaveri</option>
                        <option value="Godavari">Godavari</option>
                    </select>`;
            }

            eventOptionsGroup.innerHTML = options;
            eventOptionsGroup.style.display = options ? 'block' : 'none';
        }

        function showGamesGroup() {
            const category = document.getElementById('gameCategoryGroup').value;
            const gamesGroup = document.getElementById('gamesGroup');
            const teamMembers = document.getElementById('teamMembers');
            let options = '';

            if (category === 'Indoor') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Indoor Game</option>
                        <option value="Carroms">Carroms</option>
                        <option value="Chess">Chess</option>
                        <option value="Table Tennis">Table Tennis</option>
                    </select>`;
            } else if (category === 'Outdoor') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Outdoor Game</option>
                        <option value="Volleyball">Volleyball</option>
                        <option value="Football">Football</option>
                        <option value="Kabaddi">Kabaddi</option>
                        <option value="Kho-Kho">Kho-Kho</option>
                        <option value="Cricket">Cricket</option>
                        <option value="Basketball">Basketball</option>
                    </select>`;
            } else if (category === 'RacquetGames') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Racket Game</option>
                        <option value="Tennis">Tennis</option>
                        <option value="Badminton">Badminton</option>
                        <option value="Table Tennis">Table Tennis</option>
                    </select>`;
            } else if (category === 'Athletics') {
                options = `
                    <select name="game" onchange="showTeamMembers(this.value)" required>
                        <option value="" disabled selected>Select Athletics Game</option>
                        <option value="Relay">Relay</option>
                        <option value="Tug of War">Tug of War</option>
                    </select>`;
            }

            gamesGroup.innerHTML = options;
            gamesGroup.style.display = options ? 'block' : 'none';
            teamMembers.innerHTML = '';
            teamMembers.style.display = 'none';
        }

        function showTeamMembers(game) {
            const teamMembers = document.getElementById('teamMembers');
            let memberFields = '';

            // Default team size for all games
            let teamSize = 2; // Default for most games

            if (game === 'Cricket' || game === 'Football') {
                teamSize = 10;
            } else if (game === 'Basketball' || game === 'Volleyball') {
                teamSize = 5;
            } else if (game === 'Kabaddi' || game === 'Kho-Kho') {
                teamSize = 6;
            } else if (game === 'Tennis' || game === 'Badminton' || game === 'Table Tennis') {
                teamSize = 1;
            } else if (game === 'Relay' || game === 'Tug of War') {
                teamSize = 3;
            }

            for (let i = 2; i <= teamSize; i++) {
                memberFields += `
                    <div class="team-member">
                        <input type="text" name="member_name[]" placeholder="Player ${i} Name" required>
                        <input type="text" name="member_reg[]" placeholder="Player ${i} Registration Number" required>
                    </div>`;
            }

            teamMembers.innerHTML = memberFields;
            teamMembers.style.display = memberFields ? 'block' : 'none';
        }
    </script>
</body>
</html>