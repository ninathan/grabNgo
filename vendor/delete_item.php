<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["item_id"])) {
    $item_id = $_POST["item_id"];
    $vendor_id = $_SESSION["vendor_id"];

    // Debugging output
    error_log("Attempting to delete item ID: $item_id for vendor ID: $vendor_id");

    // Check if the item exists for this vendor
    $checkStmt = $pdo->prepare("SELECT id FROM menu_items WHERE id = ? AND vendor_id = ?");
    $checkStmt->execute([$item_id, $vendor_id]);

    if ($checkStmt->rowCount() == 0) {
        error_log("Item ID $item_id does not exist for vendor ID $vendor_id");
        echo "error: item not found";
        exit;
    }

    // Delete the item
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ? AND vendor_id = ?");
    $stmt->execute([$item_id, $vendor_id]);

    if ($stmt->rowCount() > 0) {
        error_log("Item ID $item_id successfully deleted.");
        echo "success";
    } else {
        error_log("Failed to delete item ID $item_id.");
        echo "error: delete failed";
    }
} else {
    error_log("Invalid request to delete_item.php");
    echo "error: invalid request";
}
?>
