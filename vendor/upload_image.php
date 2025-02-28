<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {
    $targetDir = "../uploads/"; // Folder to store images
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allow only certain file types
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            echo str_replace("../", "", $targetFilePath); // Return file path for database
        } else {
            echo "error";
        }
    } else {
        echo "invalid";
    }
} else {
    echo "no_file";
}
?>
