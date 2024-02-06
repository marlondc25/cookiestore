<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cookiestore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Export data to Excel
// ... (Your existing export code)

// Clear orders data from the database
$sql = "DELETE FROM orders";
if ($conn->query($sql) === TRUE) {
    echo "Orders data cleared successfully";
} else {
    echo "Error clearing orders data: " . $conn->error;
}

$conn->close();
?>
