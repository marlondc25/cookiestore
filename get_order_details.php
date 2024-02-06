<?php
// Include the database connection file
include 'db_connect.php';

// Check if the orderId parameter is set
if (isset($_GET['orderId'])) {
    $orderId = intval($_GET['orderId']);

    // Fetch order details from the database
    $orderDetails = getOrderDetails($orderId);

    // Output order details as JSON
    header('Content-Type: application/json');
    echo json_encode($orderDetails);
} else {
    // If orderId is not set, return an error message
    header('Content-Type: application/json');
    echo json_encode(['error' => 'orderId parameter is missing']);
}

// Function to fetch order details by order ID
function getOrderDetails($orderId)
{
    global $conn;
    $orderId = mysqli_real_escape_string($conn, $orderId);
    $sql = "SELECT * FROM orders WHERE order_id = $orderId";
    $result = $conn->query($sql);
    
    // Check if the query was successful
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return ['error' => 'Order not found'];
    }
}

// Close the database connection
$conn->close();
?>
