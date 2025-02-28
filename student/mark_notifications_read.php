<?php
session_start();
require '../db.php';

$student_id = $_SESSION["student_id"];

$stmt = $pdo->prepare("UPDATE orders SET notification_status = 'Read' WHERE student_id = ?");
$stmt->execute([$student_id]);

echo "success";
?>
