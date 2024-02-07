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
    } elseif ($action === 'changeStatus') {

        $newStatus = $_POST['new_status'];

        // Update the order with the new status
        $updateStatusSql = "UPDATE orders SET status = ? WHERE order_id = ?";
        $updateStatusStmt = $conn->prepare($updateStatusSql);
        $updateStatusStmt->bind_param("si", $newStatus, $order_id);

        if ($updateStatusStmt->execute()) {
            echo json_encode(["status" => 'success', "message" => 'Status updated successfully', "new_status" => $newStatus]);
        } else {
            echo json_encode(["status" => 'error', "message" => 'Error updating status: ' . $updateStatusStmt->error]);
        }
        $updateStatusStmt->close();
    
     } else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

$conn->close();
?>