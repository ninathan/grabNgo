<?php
session_start();
require '../db.php';

// Check if order_id is set
if (!isset($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = $_GET['order_id'];

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Fetch ordered items (Check column name in menu_items table)
$stmt = $pdo->prepare("SELECT menu_items.item_name, order_items.quantity
                       FROM order_items
                       JOIN menu_items ON order_items.menu_item_id = menu_items.id
                       WHERE order_items.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../assets/vendor.css">
    <script src="dark-mode.js"></script>
</head>
<body>
    <div class="order-container">
        <h2>Order Details</h2>

        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

        <h3>Ordered Items:</h3>
        <ul>
            <?php foreach ($items as $item): ?>
                <li><?php echo htmlspecialchars($item['item_name']) . " - Quantity: " . $item['quantity']; ?></li>
            <?php endforeach; ?>
        </ul>

        <a href="dashboard.php" class="back-btn-2">Back to Dashboard</a>
    </div>
</body>
</html>
