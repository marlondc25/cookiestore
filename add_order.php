<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $customerName = $_POST['customer_name'];
    $quantity = $_POST['quantity'];
    $cookieType = $_POST['cookie_type'];
    $price = $_POST['price'];

    // Assuming your database table has columns: customer_name, quantity, cookie_type, price
    $sql = "INSERT INTO orders (customer_name, quantity, cookie_type, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $customerName, $quantity, $cookieType, $price);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Order added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error adding order: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

$conn->close();
?>
