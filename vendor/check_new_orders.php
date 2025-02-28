<?php
session_start();
require '../db.php';

$vendor_id = $_SESSION["vendor_id"];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE stall_id IN (SELECT id FROM stalls WHERE vendor_id = ?) AND status = 'Pending'");
$stmt->execute([$vendor_id]);

echo $stmt->fetchColumn(); // Return the number of new orders
?>
