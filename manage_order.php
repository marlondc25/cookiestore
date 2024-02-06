<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['order_id'])) {
    $action = $_POST['action'];
    $order_id = $_POST['order_id'];

    if ($action === 'delete') {
        $sql = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Order deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting order: " . $stmt->error]);
        }

        $stmt->close();
    } elseif ($action === 'edit') {
        $new_quantity = $_POST['new_quantity'];
        $new_cookie_type = $_POST['new_cookie_type'];

        // Retrieve the unit price based on the selected cookie type
        $unitPriceSql = "SELECT price FROM cookie_prices WHERE cookie_type = ?";
        $unitPriceStmt = $conn->prepare($unitPriceSql);
        $unitPriceStmt->bind_param("s", $new_cookie_type);
        $unitPriceStmt->execute();
        $unitPriceResult = $unitPriceStmt->get_result();

        if ($unitPriceResult->num_rows > 0) {
            $row = $unitPriceResult->fetch_assoc();
            $unitPrice = $row['price'];

            // Calculate the new price
            $new_price = $new_quantity * $unitPrice;

            // Update the order with new quantity, cookie type, and price
            $updateSql = "UPDATE orders SET quantity = ?, cookie_type = ?, price = ? WHERE order_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("isdi", $new_quantity, $new_cookie_type, $new_price, $order_id);

            if ($updateStmt->execute()) {
                echo json_encode(["status" => 'success', "message" => 'Order updated successfully', "new_price" => $new_price]);
            } else {
                echo json_encode(["status" => 'error', "message" => 'Error updating order: ' . $updateStmt->error]);
            }
            $updateStmt->close();
        } else {
            echo json_encode(["status" => 'error', "message" => 'Invalid cookie type']);
        }

        $unitPriceStmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

$conn->close();

function getPriceByCookieType($cookie_type) {
    // Implement your logic to retrieve the price based on cookie type
    // For now, assuming you have this function elsewhere in your code
    // You may replace this with your actual logic
    return 60; // Default price for demonstration purposes
}
?>
