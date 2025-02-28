<?php
session_start();
require '../db.php';

// Ensure vendor is logged in
if (!isset($_SESSION["vendor_id"]) || $_SESSION["role"] !== "vendor") {
    echo "<tr><td colspan='5'>Session expired. Please log in again.</td></tr>";
    exit;
}

$vendor_id = $_SESSION["vendor_id"];

// Fetch vendor's stall ID
$stallStmt = $pdo->prepare("SELECT id FROM stalls WHERE vendor_id = ?");
$stallStmt->execute([$vendor_id]);
$stall = $stallStmt->fetch(PDO::FETCH_ASSOC);

if (!$stall) {
    echo "<tr><td colspan='5'>No stall found for this vendor.</td></tr>";
    exit;
}

$stall_id = $stall["id"];

// Fetch menu items
$menuStmt = $pdo->prepare("SELECT id, item_name, price, status, picture_url FROM menu_items WHERE stall_id = ?");
$menuStmt->execute([$stall_id]);
$menuItems = $menuStmt->fetchAll();

if (!$menuItems) {
    echo "<tr><td colspan='5'>No menu items found.</td></tr>";
    exit;
}

foreach ($menuItems as $item) {
    echo "<tr>";
    echo "<td><img src='" . htmlspecialchars($item['picture_url']) . "' alt='Item Image' width='50'></td>";
    echo "<td>" . htmlspecialchars($item['item_name']) . "</td>";
    echo "<td>₱" . number_format($item['price'], 2) . "</td>";

    // Availability Toggle Button
    $checked = $item['status'] === 'Available' ? "checked" : "";
    echo "<td>
        <label class='switch'>
            <input type='checkbox' class='toggle-status' data-id='" . $item['id'] . "' $checked>
            <span class='slider round'></span>
        </label>
    </td>";

    echo "<td><button class='delete-item' data-id='" . $item['id'] . "'>Delete</button></td>";
    echo "</tr>";
}
?>



<!-- $stmt = $pdo->prepare("SELECT id, item_name, price, picture_url, status FROM menu_items");
$stmt->execute(); -->