<?php
session_start();
require '../db.php';

$student_id = $_SESSION["student_id"];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE student_id = ? AND notification_status = 'Unread'");
$stmt->execute([$student_id]);
$unreadCount = $stmt->fetchColumn();

echo $unreadCount; // Should return a number >0 if there are notifications
?>
