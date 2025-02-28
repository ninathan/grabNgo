<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id, student_id, first_name, last_name, password FROM users WHERE email = ? AND role = 'student'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        // Start session
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["student_id"] = $user["student_id"];
        $_SESSION["first_name"] = $user["first_name"];
        $_SESSION["last_name"] = $user["last_name"];
        $_SESSION["role"] = "student";

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid email or password.";
    }
}
?>
