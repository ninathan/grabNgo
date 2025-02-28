<?php
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $stall_id = $_POST["stall_id"];

    // Validate fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($stall_id)) {
        die("All fields are required.");
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        die("Email is already registered.");
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert vendor into users table
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, student_id, created_at) 
                           VALUES (?, ?, ?, ?, 'vendor', NULL, NOW())");
    $success = $stmt->execute([$first_name, $last_name, $email, $hashed_password]);

    if ($success) {
        // Fetch the newly created vendor's ID
        $vendor_id = $pdo->lastInsertId();

        // Assign vendor to the selected stall
        $stmt = $pdo->prepare("UPDATE stalls SET vendor_id = ? WHERE id = ?");
        $stmt->execute([$vendor_id, $stall_id]);

        echo "Vendor registered successfully!";
        header("Location: login.html"); // Redirect to login page
        exit;
    } else {
        echo "Error registering vendor.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Signup</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<a href="../vendor/login.html" class="back-btn">← Back to Home</a>
    <div class="signup-container">
        <h2>Vendor Signup</h2>
        <form action="" method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
        
            <!-- Select Stall -->
            <select name="stall_id" required>
                <option value="">Select Stall</option>
                <?php
                // Fetch all stalls that don't have an assigned vendor
                $stmt = $pdo->query("SELECT id, name FROM stalls WHERE vendor_id IS NULL");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <button type="submit" class="button">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.html">Login here</a></p>
    </div>
</body>
</html>
