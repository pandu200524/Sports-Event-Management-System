<?php 
session_start(); 
$error = isset($_GET['error']) ? "Invalid username or password!" : "";
$message = isset($_GET['message']) && $_GET['message'] == 'logged_out' ? "You have been logged out successfully!" : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sports Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            text-align: center;
        }
        header {
            background-color: #484622;
            color: white;
            padding: 20px;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
        }
        .login-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        .login-card input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-card button {
            width: 100%;
            padding: 10px;
            background-color: #484622;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-card button:hover {
            background-color: #3a3a1e;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
        .success-message {
            color: green;
            font-size: 14px;
        }
        footer {
            background-color: #484622;
            color: white;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<header>
    <h1>Sports Admin</h1>
</header>

<div class="container">
    <div class="login-card">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($message): ?>
            <p class="success-message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="logincheck.php" method="post">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2025 SRM University AP - All Rights Reserved.</p>
</footer>

</body>
</html>