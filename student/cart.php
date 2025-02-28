<?php 
session_start();
include '../db.php';

// Check if menu items were selected
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['menu_items'])) {
    $_SESSION['cart'] = [];
    $_SESSION['stall_id'] = $_POST['stall_id']; // Store stall_id in session

    foreach ($_POST['menu_items'] as $item_id) {
        $quantity = isset($_POST['quantity'][$item_id]) ? (int)$_POST['quantity'][$item_id] : 1;
        $_SESSION['cart'][$item_id] = max(1, $quantity); // Ensure at least 1 item
    }
}

// Retrieve stall_id from session if available
$stall_id = $_SESSION['stall_id'] ?? $_POST['stall_id'] ?? '';

// Check if cart is empty and redirect to menu
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: menu.php?stall_id=" . urlencode($stall_id));
    exit();
}

// Fetch cart items
$cart_items = $_SESSION['cart'];
$total_price = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="../assets/cart.css">
    <script src="dark-mode.js"></script>
</head>
<body>
    <nav class="navbar">
        <h2>Cart</h2>
        <a href="menu.php?stall_id=<?php echo urlencode($stall_id); ?>" class="back-btn">Back to Menu</a>
    </nav>
    <div class="container">
        <h3>Your Cart</h3>
        <div class="cart-items">
            <?php foreach ($cart_items as $id => $quantity): ?>
                <?php
                // Fetch item details
                $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $item = $result->fetch_assoc();
                $stmt->close();
                ?>
                <div class='cart-item'>
                    <img src="<?php echo htmlspecialchars($item['picture_url']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                    <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                    <p>Quantity: <?php echo $quantity; ?></p>
                    <p>Price: ₱<?php echo number_format($item['price'] * $quantity, 2); ?></p>
                    <a href="remove_from_cart.php?id=<?php echo $id; ?>" class="remove-btn">Remove</a>
                </div>
                <?php $total_price += $item['price'] * $quantity; ?>
            <?php endforeach; ?>
        </div>
        <h3>Total Price: ₱<?php echo number_format($total_price, 2); ?></h3>
        <a href="place_order.php" class="checkout-btn">Place Order</a>
    </div>
</body>
</html>
