<?php
session_start();
include '../db.php';

// Check if order_id is set
if (!isset($_GET['order_id'])) {
    echo "Invalid order ID.";
    exit();
}


$order_id = $_GET['order_id'];

if (!isset($_GET['stall_id'])) {
    die("Invalid request. Stall ID not found.");
}

$stall_id = $_GET['stall_id'];

// Fetch stall details
$stall_query = "SELECT name FROM stalls WHERE id = ?";
$stmt = $conn->prepare($stall_query);
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$result = $stmt->get_result();
$stall = $result->fetch_assoc();
$stmt->close();

// Check if stall was found
if (!$stall) {
    die("Stall not found.");
}




// Fetch order details
$order_query = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($order_query);


// ✅ Check if prepare() failed
if (!$stmt) {
    die("Error preparing statement (orders query): " . $conn->error);
}

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// If order not found, show error
if (!$order) {
    die("Order not found.");
}

// Fetch ordered items
$items_query = "SELECT menu_items.item_name, order_items.quantity 
                FROM order_items 
                JOIN menu_items ON order_items.menu_item_id = menu_items.id 
                WHERE order_items.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

// Fetch items into an array
$ordered_items = [];
while ($row = $items_result->fetch_assoc()) {
    $ordered_items[] = $row;
}
$stmt->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="/grabNgo/assets/order.css">
    <script src="dark-mode.js"></script>
    



</head>
<body>
    <div class="container">
        <h2>Order Confirmation</h2>
        <p><strong>Please pay at the counter of <?php echo htmlspecialchars($stall['name'] ?? 'Unknown Stall'); ?></strong></p>
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
        
        <h3>Ordered Items:</h3>
<ul>
    <?php if (!empty($ordered_items)): ?>
        <?php foreach ($ordered_items as $item): ?>
            <li><?php echo htmlspecialchars($item['item_name']) . " - Quantity: " . $item['quantity']; ?></li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No items found for this order.</li>
    <?php endif; ?>
</ul>


        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
