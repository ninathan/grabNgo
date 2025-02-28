<?php 
session_start();
include '../db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../student/login.php");
    exit();
}

// Get stall ID from URL
if (!isset($_GET['stall_id'])) {
    echo "Invalid request.";
    exit();
}
$stall_id = $_GET['stall_id'];

// Fetch stall details
$stall_query = "SELECT * FROM stalls WHERE id = ?";
$stmt = $conn->prepare($stall_query);
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$stall = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch menu items for this stall
$menu_query = "SELECT * FROM menu_items WHERE stall_id = ? AND status = 'Available'";
$stmt = $conn->prepare($menu_query);
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$menu_items = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - <?php echo htmlspecialchars($stall['name']); ?></title>
    <link rel="stylesheet" href="../assets/menu.css">
    <script src="dark-mode.js"></script>
    <script src="../assets/script.js"></script>
   


</head>
<body>
    <nav class="navbar">
        <h2><?php echo htmlspecialchars($stall['name']); ?> Menu</h2>
        <a href="dashboard.php" class="back-btn">Back to Stalls</a>
    </nav>
    
    <div class="container">
        <h3>Menu Items</h3>
        <form action="cart.php" method="POST">
            <input type="hidden" name="stall_id" value="<?php echo $stall_id; ?>">
            <div class="menu-grid">
                <?php while ($item = $menu_items->fetch_assoc()): ?>
                    <div class="menu-card">
                    <img src="<?php echo htmlspecialchars($item['picture_url']); ?>" 
     alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
     class="menu-image">
                        <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                        <p>Price: ₱<?php echo number_format($item['price'], 2); ?></p>
                        <label>
                            <input type="checkbox" name="menu_items[]" value="<?php echo $item['id']; ?>"> Add to Order
                        </label>
                        <div class="quantity-selector">
    <button type="button" class="qty-btn minus" onclick="updateQuantity(this, -1)">−</button>
    <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="1" min="1" class="quantity-input" readonly>
    <button type="button" class="qty-btn plus" onclick="updateQuantity(this, 1)">+</button>
</div>

                    </div>
                <?php endwhile; ?>
            </div>
            <button type="submit" class="order-btn">Proceed to Checkout</button>
        </form>
    </div>
</body>
</html>
