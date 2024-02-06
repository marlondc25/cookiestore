<?php
// Start the session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredUsername = $_POST["username"];
    $enteredPassword = $_POST["password"];

    $correctUsername = "admin";
    $correctPassword = "admin";

    if ($enteredUsername == $correctUsername && $enteredPassword == $correctPassword) {
        // Set the session variable upon successful login
        $_SESSION['username'] = $enteredUsername;

        // Redirect to order_form.php on successful login
        header("Location: order_form.php");
        exit();
    } else {
        // Display an improved error message
        echo '<div class="notification">Incorrect username or password. Please try again!</div>';
    }
}
?>
<link href="https://fonts.cdnfonts.com/css/mastery-kingdom" rel="stylesheet">
                
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        footer {
            background color:
            padding: 
            text-align: center;
        }
        .tagline {
            font-size: 25px;
            font-family: 'brush script mt';
        }
        .credits-footer {
            font-size: 12px;
            font-family: 'italic', sans-serif;
        }

        .developer-name {
            font-weight: bold;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: url('your-background-image.jpg') center/cover no-repeat; /* Replace 'your-background-image.jpg' with your actual image path */
        }

        .login-container {
            text-align: center;
            font-family: 'Mastery Kingdom', sans-serif;                                               
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        input {
            padding: 10px;
            margin: 10px 0;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            margin-top: 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background-color: #45a049;
        }

        .notification {
            color: #ff0000;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login to Brewed Bliss & Cookie Bits Café</h2>

    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
        <div class="tagline">
        <p>"From Bean to Cup, Pure Coffee Love."</p>
    </div>
<footer>
    <div class="credits-footer">
        <p>Developed by <span class="developer-name">Marlon Dela Cruz</span></p>
            <p>&copy; 2024 Brewed Bliss & Cookie Bits Web App</p>
    </div>
    </footer>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $enteredUsername = $_POST["username"];
        $enteredPassword = $_POST["password"];

        $correctUsername = "admin";
        $correctPassword = "admin";

        if ($enteredUsername == $correctUsername && $enteredPassword == $correctPassword) {
            // Redirect to order_form.php on successful login
            header("Location: order_form.php");
            exit();
        } else {
            // Display an improved error message
            echo '<div class="notification">Incorrect username or password. Please try again!</div>';
        }
    }
    ?>
</div>

</body>
</html>
