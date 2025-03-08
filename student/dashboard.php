<?php
session_start();
include '../db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../student/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details using PDO
$student_query = "SELECT first_name FROM users WHERE student_id = ?";
$stmt = $pdo->prepare($student_query);
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
$student_name = htmlspecialchars($student['first_name'] ?? 'Student'); // Fallback in case of error

// Fetch stalls using MySQLi
$query = "SELECT * FROM stalls";
$result = mysqli_query($conn, $query);

// Fetch top 5 popular menu items
$popular_items_query = "
    SELECT menu_items.item_name, menu_items.picture_url, COUNT(orders.id) AS times_ordered
    FROM orders 
    JOIN order_items ON orders.id = order_items.order_id
    JOIN menu_items ON order_items.menu_item_id = menu_items.id
    GROUP BY menu_items.id, menu_items.item_name, menu_items.picture_url
    ORDER BY times_ordered DESC 
    LIMIT 5";
$popular_stmt = $pdo->prepare($popular_items_query);
$popular_stmt->execute();
$popular_items = $popular_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/student_dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<script>
setInterval(function() {
    $.get("../keep_session_alive.php"); 
}, 300000); 
</script>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <h2>Welcome, <?php echo $student_name; ?>!</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
        <a href="order_status.php" class="order-status-btn">View Order Status</a>
    </nav>

    <!-- Dashboard Container -->
    <div class="container">
        <h3>Available Stalls</h3>
        <div class="stalls-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class='stall-card'>
                    <h4><?= htmlspecialchars($row['name']) ?></h4>
                    <a href='menu.php?stall_id=<?= htmlspecialchars($row['id']) ?>' class='menu-btn'>View Menu</a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Top 5 Popular Items Section -->
        <div class="popular-items-section">
    <h3>🔥 Top 5 Popular Items</h3>
    <div class="popular-items-grid">
        <?php foreach ($popular_items as $item): ?>
            <div class="popular-item-card">
                <img src="../uploads/<?php echo htmlspecialchars($item['picture_url']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                <p>Ordered <?php echo $item['times_ordered']; ?> times</p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    </div>

</body>

<script>
$(document).ready(function () {
    function checkNotifications() {
        $.get("check_notifications.php", function (data) {
            console.log("Notification Count:", data); // Debugging line
            if (parseInt(data) > 0) {
                $("#notification-bell").text(data).show();
            } else {
                $("#notification-bell").hide();
            }
        });
    }

    setInterval(checkNotifications, 5000); // Check every 5 seconds

    $("#notification-icon").click(function () {
        $.post("mark_notifications_read.php", function () {
            $("#notification-bell").hide();
        });
    });
});
</script>

</html>
