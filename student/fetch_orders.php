<?php 
session_start();
include '../db.php';

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$student_id = $_SESSION['student_id'];

$query = "SELECT * FROM orders WHERE student_id = ? ORDER BY order_time DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$student_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$orders) {
    echo json_encode(["status" => "empty"]);
    exit();
}

$html = "";
foreach ($orders as $order) {
    $statusClass = "";
    switch ($order['status']) {
        case 'Pending': $statusClass = "status-pending"; break;
        case 'Preparing': $statusClass = "status-preparing"; break;
        case 'Completed': $statusClass = "status-completed"; break;
    }

    $html .= "
        <div class='order-item'>
            <p><strong>Order ID:</strong> " . htmlspecialchars($order['id']) . "</p>
            <p><strong>Status:</strong> <span class='order-status $statusClass'>" . htmlspecialchars($order['status']) . "</span></p>
            <p><strong>Total Price:</strong> ₱" . htmlspecialchars($order['total_price']) . "</p>
            <p><strong>Order Time:</strong> " . htmlspecialchars($order['order_time']) . "</p>
        </div>";
}

echo json_encode(["status" => "success", "html" => $html]);
?>
