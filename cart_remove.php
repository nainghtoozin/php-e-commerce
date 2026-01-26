<?php
session_start();

$product_id = $_POST['product_id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    $_SESSION['error'] = "Invalid request";
    header("Location: cart.php");
    exit;
}

// Remove item from cart
if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
    $_SESSION['success'] = "Item removed from cart";
} else {
    $_SESSION['error'] = "Item not found in cart";
}

header("Location: cart.php");
exit;
?>