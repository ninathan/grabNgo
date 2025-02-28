<?php
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password hashing

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "Error: Email already exists.";
        exit;
    }

    // Find an available stall that is NOT assigned to a vendor
    $stmt = $pdo->prepare("SELECT id FROM stalls WHERE vendor_id IS NULL LIMIT 1");
    $stmt->execute();
    $stall = $stmt->fetch();

    if (!$stall) {
        echo "Error: No available stalls.";
        exit;
    }

    $stall_id = $stall["id"];

    // Insert vendor into users table
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'vendor')");
    if ($stmt->execute([$first_name, $last_name, $email, $hashed_password])) {
        $vendor_id = $pdo->lastInsertId(); // Get the last inserted user ID

        // Assign the stall to the vendor
        $stmt = $pdo->prepare("UPDATE stalls SET vendor_id = ? WHERE id = ?");
        $stmt->execute([$vendor_id, $stall_id]);

        header("Location: login.html?success=1"); // Redirect to vendor login
        exit;
    } else {
        echo "Error: Could not register vendor.";
    }
}
?>
