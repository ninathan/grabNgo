<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["order_id"])) {
    $order_id = $_POST["order_id"];

    // Update the order status and set notification to 'Unread'
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
    
    if ($stmt->execute([$order_id])) { // Only execute once
        echo "success";
    } else {
        echo "error";
    }
}
?>
