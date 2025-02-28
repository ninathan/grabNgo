<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST["item_id"];

    // Get current status
    $stmt = $pdo->prepare("SELECT status FROM menu_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();

    if ($item) {
        $new_status = ($item["status"] === "Available") ? "Out Of Stock" : "Available";

        // Update status in database
        $update_stmt = $pdo->prepare("UPDATE menu_items SET status = ? WHERE id = ?");
        if ($update_stmt->execute([$new_status, $item_id])) {
            echo $new_status; // Send new status back to frontend
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
?>
