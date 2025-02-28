<?php
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST["student_id"]);
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password hashing

    // Check if email or student_id already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR student_id = ?");
    $stmt->execute([$email, $student_id]);
    
    if ($stmt->rowCount() > 0) {
        echo "Error: Email or Student ID already exists.";
        exit;
    }

    // Insert student into database
    $stmt = $pdo->prepare("INSERT INTO users (student_id, first_name, last_name, email, password, role) 
                           VALUES (?, ?, ?, ?, ?, 'student')");
    if ($stmt->execute([$student_id, $first_name, $last_name, $email, $hashed_password])) {
        header("Location: login.html?success=1"); // Redirect to login page
        exit;
    } else {
        echo "Error: Could not register student.";
    }
}
?>
