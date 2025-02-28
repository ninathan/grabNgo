<?php 
session_start();
require '../db.php';

// Ensure vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// Get the stall_id associated with the vendor
$stmt = $pdo->prepare("SELECT id FROM stalls WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$stall = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stall) {
    die("Error: Stall not found for this vendor.");
}

$stall_id = $stall['id'];

// Fetch orders for this stall
$stmt = $pdo->prepare("SELECT * FROM orders WHERE stall_id = ? ORDER BY order_time DESC");
$stmt->execute([$stall_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Orders</title>
    <link rel="stylesheet" href="../assets/vendor.css">
    <script src="dark-mode.js"></script>
   


</head>
<body>
    <div class="container">
        <h2>Orders for Your Stall</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Student ID</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Order Time</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['student_id']); ?></td>
                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_time']); ?></td>
                    <td><a href="vendor_order_details.php?order_id=<?php echo $order['id']; ?>">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
