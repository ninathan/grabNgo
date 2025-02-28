<?php
session_start();
require '../db.php';

// Suppress all errors & warnings
error_reporting(0);

$vendor_id = $_SESSION["vendor_id"] ?? 0;

// Fetch stall_id for the vendor
$stmt = $pdo->prepare("SELECT id FROM stalls WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$stall = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stall) {
    echo "<script>alert('No stall found.'); window.location.href = 'vendor_dashboard.php';</script>";
    exit;
}

$stall_id = $stall["id"];
$item_name = $_POST["item_name"] ?? '';
$price = $_POST["price"] ?? 0;

$targetDir = "../uploads/";
$originalFileName = basename($_FILES["item_image"]["name"]);
$fileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

// Generate unique filename
$fileName = time() . "_" . uniqid() . "." . $fileType;
$targetFilePath = $targetDir . $fileName;


// Move uploaded file
if (@move_uploaded_file($_FILES["item_image"]["tmp_name"], $targetFilePath)) {
    @resizeImage($targetFilePath); // Resize function

    // Insert into database
    $query = "INSERT INTO menu_items (stall_id, vendor_id, item_name, price, picture_url, status) 
              VALUES (?, ?, ?, ?, ?, 'Available')";
    $stmt = $pdo->prepare($query);



    if ($stmt->execute([$stall_id, $vendor_id, $item_name, $price, $targetFilePath])) {
        echo "<script>alert('Item added successfully!'); window.location.href = 'vendor_dashboard.php';</script>";
    } else {
        echo "<script>alert('Database insert failed.'); window.location.href = 'vendor_dashboard.php';</script>";
    }
} else {
    echo "<script>alert('File upload failed.'); window.location.href = 'vendor_dashboard.php';</script>";
}

/**
 * Resize Image Function
 */
function resizeImage($filePath, $maxWidth = 300, $maxHeight = 300) {
    list($origWidth, $origHeight, $imageType) = @getimagesize($filePath);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $srcImage = @imagecreatefromjpeg($filePath);
            break;
        case IMAGETYPE_PNG:
            $srcImage = @imagecreatefrompng($filePath);
            break;
        case IMAGETYPE_GIF:
            $srcImage = @imagecreatefromgif($filePath);
            break;
        default:
            return false;
    }

    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = intval($origWidth * $ratio);
    $newHeight = intval($origHeight * $ratio);

    $newImage = @imagecreatetruecolor($newWidth, $newHeight);

    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        @imagealphablending($newImage, false);
        @imagesavealpha($newImage, true);
        $transparent = @imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        @imagefill($newImage, 0, 0, $transparent);
    }

    @imagecopyresampled($newImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            @imagejpeg($newImage, $filePath, 90);
            break;
        case IMAGETYPE_PNG:
            @imagepng($newImage, $filePath);
            break;
        case IMAGETYPE_GIF:
            @imagegif($newImage, $filePath);
            break;
    }

    @imagedestroy($srcImage);
    @imagedestroy($newImage);

    return true;
}
?>
