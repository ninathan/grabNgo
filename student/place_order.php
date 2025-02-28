<?php
session_start();
include '../db.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Your cart is empty.");
}

// Get student ID
$student_id = $_SESSION['student_id']; 

echo "Checking student ID: $student_id <br>";
$status = "Pending"; 
$total_price = 0;
$stall_id = null; 

// Validate student ID
$check_student = $conn->prepare("SELECT student_id FROM users WHERE student_id = ?");
$check_student->bind_param("s", $student_id);
$check_student->execute();
$check_student->store_result();

if ($check_student->num_rows == 0) {
    die("Error: Student ID does not exist in the database.");
}

$check_student->bind_result($actual_student_id);
$check_student->fetch();
$check_student->close();


// Fetch stall_id and calculate total price
$first_item = true;
foreach ($_SESSION['cart'] as $item_id => $quantity) {
    $stmt = $conn->prepare("SELECT stall_id, price FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->bind_result($fetched_stall_id, $price);
    $stmt->fetch();
    $stmt->close();

    // Ensure all items belong to the same stall
    if ($first_item) {
        $stall_id = $fetched_stall_id;
        $first_item = false;
    } elseif ($stall_id !== $fetched_stall_id) {
        echo "<script>alert('Error: You can only order from one stall at a time.'); window.location.href = 'cart.php';</script>";
        exit();
    }

    // Calculate total price
    $total_price += $price * $quantity;
}

// Ensure stall_id is not null
if ($stall_id === null) {
    die("Error: Unable to determine stall ID for the order.");
}

// Insert new order
$order_query = "INSERT INTO orders (student_id, stall_id, total_price, status, order_time) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($order_query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("sids", $student_id, $stall_id, $total_price, $status); // Fix: use student_id instead of actual_student_id

if (!$stmt->execute()) {
    die("Error placing order: " . $stmt->error);
}
$order_id = $stmt->insert_id;
$stmt->close();

// Insert ordered items
foreach ($_SESSION['cart'] as $item_id => $quantity) {
    $item_query = "INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($item_query);
    if (!$stmt) {
        die("Error preparing order_items statement: " . $conn->error);
    }
    $stmt->bind_param("iii", $order_id, $item_id, $quantity);
    if (!$stmt->execute()) {
        die("Error inserting order item: " . $stmt->error);
    }
    $stmt->close();
}

// Clear cart after order placement
unset($_SESSION['cart']);

// Redirect to order confirmation page
header("Location: order_confirmation.php?order_id=" . $order_id . "&stall_id=" . $stall_id);
exit();
?>
