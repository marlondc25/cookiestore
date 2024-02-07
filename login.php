<?php

// Start the session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredUsername = $_POST["username"];
    $enteredPassword = $_POST["password"];

    // Check static credentials
    $staticCredentials = array(
        "admin" => "admin",
        "marlon" => "mdelacruz",
        "arielle" => "amarah2020"
    );

    if (array_key_exists($enteredUsername, $staticCredentials) && $enteredPassword == $staticCredentials[$enteredUsername]) {
        // Set the session variable upon successful login
        $_SESSION['username'] = $enteredUsername;

        // Redirect to order_form.php on successful login
        header("Location: order_form.php");
        exit();
    } else {
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "cookiestore");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare a SQL statement
        $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");

        // Bind parameters
        $stmt->bind_param("s", $enteredUsername);

        // Execute the statement
        $stmt->execute();

        // Store the result
        $stmt->store_result();

        // Check if a row was found
        if ($stmt->num_rows > 0) {
            // Bind result variables
            $stmt->bind_result($dbUsername, $dbPassword);

            // Fetch the values
            $stmt->fetch();

            // Close the statement
            $stmt->close();
            $conn->close();

            // Verify the entered password against the hashed password in the database
            if (password_verify($enteredPassword, $dbPassword)) {
                // Set the session variable upon successful login
                $_SESSION['username'] = $enteredUsername;

                // Redirect to order_form.php on successful login
                header("Location: order_form.php");
                exit();
            }
        }

        // If execution reaches this point, it means the username or password is incorrect
        // Close the statement and connection
        $stmt->close();
        $conn->close();

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
    background: url('your-background-image.jpg') center/cover no-repeat fixed; /* Replace 'your-background-image.jpg' with the correct relative path */
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
    <h2>Login to COFFEE BUDDIES</h2>
    <div class="tagline">
        <p>"From Bean to Cup, Pure Coffee Love."</p>
    </div>

    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
        
<footer>
    <div class="credits-footer">
        <p>Developed by <span class="developer-name">Marlon Dela Cruz</span></p>
            <p>&copy; 2024 Brewed Bliss & Cookie Bits Web App</p>
    </div>
    </footer>
    </form>
</div>

</body>
</html>