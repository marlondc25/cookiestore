<?php

ob_start();
// rest of your code
ob_end_flush();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cookiestore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
