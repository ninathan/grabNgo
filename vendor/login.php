<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if email exists and belongs to a vendor
    $stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name, u.password, s.id AS stall_id 
                           FROM users u 
                           LEFT JOIN stalls s ON u.id = s.vendor_id 
                           WHERE u.email = ? AND u.role = 'vendor'");
    $stmt->execute([$email]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vendor && password_verify($password, $vendor["password"])) {
        // Start session
        $_SESSION["vendor_id"] = $vendor["id"];
        $_SESSION["first_name"] = $vendor["first_name"];
        $_SESSION["last_name"] = $vendor["last_name"];
        $_SESSION["role"] = "vendor";
        $_SESSION["stall_id"] = $vendor["stall_id"]; // Store stall_id in session

        // Redirect to vendor dashboard
        header("Location: vendor_dashboard.php");
        exit;
    } else {
        echo "Invalid email or password.";
    }
}
?>
