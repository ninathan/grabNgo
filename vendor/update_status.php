<?php
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_id = $_POST["item_id"];
    $new_status = $_POST["status"];

    $updateStmt = $pdo->prepare("UPDATE menu_items SET status = ? WHERE id = ?");
    if ($updateStmt->execute([$new_status, $item_id])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
