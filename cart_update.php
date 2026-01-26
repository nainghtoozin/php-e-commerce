<?php
session_start();
include("vendor/autoload.php");

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;

$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? null;

if (!$product_id || !is_numeric($product_id) || !$quantity || !is_numeric($quantity) || $quantity < 1) {
    $_SESSION['error'] = "Invalid request";
    header("Location: cart.php");
    exit;
}

// Check if product exists in cart
if (!isset($_SESSION['cart'][$product_id])) {
    $_SESSION['error'] = "Product not found in cart";
    header("Location: cart.php");
    exit;
}

// Validate product exists and has enough stock
$productsTable = new ProductsTable(new MySQL());
$product = $productsTable->findById($product_id);

if (!$product || $product->status != 1) {
    unset($_SESSION['cart'][$product_id]);
    $_SESSION['error'] = "Product is no longer available";
    header("Location: cart.php");
    exit;
}

// Check inventory stock
$mysql = new MySQL();
$db = $mysql->connect();
$inv_stmt = $db->prepare("SELECT quantity FROM inventories WHERE product_id = ?");
$inv_stmt->execute([$product_id]);
$available_quantity = $inv_stmt->fetchColumn();

if ($available_quantity === false || $available_quantity < $quantity) {
    $available = $available_quantity === false ? 0 : $available_quantity;
    $_SESSION['error'] = "Only {$available} items available in stock";
    header("Location: cart.php");
    exit;
}

// Update cart quantity
$_SESSION['cart'][$product_id]['qty'] = $quantity;

$_SESSION['success'] = "Cart updated successfully";
header("Location: cart.php");
exit;
?>