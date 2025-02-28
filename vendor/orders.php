<?php
session_start();
require '../db.php';

$vendor_id = $_SESSION["vendor_id"];

// Fetch vendor's stall ID
$stallStmt = $pdo->prepare("SELECT id FROM stalls WHERE vendor_id = ?");
$stallStmt->execute([$vendor_id]);
$stall = $stallStmt->fetch(PDO::FETCH_ASSOC);

if (!$stall) {
    echo "<tr><td colspan='4'>No stall found for this vendor.</td></tr>";
    exit;
}

$stall_id = $stall["id"];

// Fetch all orders for the vendor's stall (not limited)
$orderStmt = $pdo->prepare("
    SELECT orders.id, orders.status, orders.order_time
    FROM orders 
    WHERE orders.stall_id = ?
    ORDER BY orders.order_time DESC
");
$orderStmt->execute([$stall_id]);
$orders = $orderStmt->fetchAll();

if (!$orders) {
    echo "<tr><td colspan='4'>No orders found.</td></tr>";
    exit;
}

// Display orders
foreach ($orders as $order) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($order['id']) . "</td>";

    // Fetch ordered items
    $itemsStmt = $pdo->prepare("
        SELECT mi.item_name, oi.quantity 
        FROM order_items oi 
        JOIN menu_items mi ON oi.menu_item_id = mi.id 
        WHERE oi.order_id = ?
    ");
    $itemsStmt->execute([$order['id']]);
    $items = $itemsStmt->fetchAll();

    echo "<td>";
    foreach ($items as $item) {
        echo htmlspecialchars($item['item_name']) . " (x" . $item['quantity'] . ")<br>";
    }
    echo "</td>";

    echo "<td>" . htmlspecialchars($order['status']) . "</td>";

    echo "<td>
            <a href='vendor_order_details.php?order_id=" . $order['id'] . "' class='view-details-btn'>View Details</a>
            <button class='update-order' data-id='" . $order['id'] . "'>Mark as Completed</button>
          </td>";
    
    // echo "</tr>";
    // echo "<td><button class='update-order' data-id='" . $order['id'] . "'>Mark as Completed</button></td>";
    // echo "</tr>";
}
?>
