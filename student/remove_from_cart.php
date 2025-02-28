<?php
session_start();

// Check if an item ID was provided
if (isset($_GET['id']) && isset($_SESSION['cart'][$_GET['id']])) {
    unset($_SESSION['cart'][$_GET['id']]); // Remove item from cart
}

// Redirect back to cart
header("Location: cart.php");
exit();
?>
