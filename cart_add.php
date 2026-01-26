<?php
session_start();
include("vendor/autoload.php");

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;

$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$product_id || !is_numeric($product_id)) {
    header("Location: index.php");
    exit;
}

// Validate product exists and is active
$productsTable = new ProductsTable(new MySQL());
$product = $productsTable->findById($product_id);

if (!$product || $product->status != 1 || $product->quantity <= 0) {
    $_SESSION['error'] = "Product is not available";
    header("Location: product.php?id=$product_id");
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
    header("Location: product.php?id=$product_id");
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart or update quantity
if (isset($_SESSION['cart'][$product_id])) {
    $new_quantity = $_SESSION['cart'][$product_id]['qty'] + $quantity;
    if ($new_quantity > $available_quantity) {
        $_SESSION['error'] = "Only {$available_quantity} items available in stock";
        header("Location: product.php?id=$product_id");
        exit;
    }
    $_SESSION['cart'][$product_id]['qty'] = $new_quantity;
} else {
    // Get product image
    $image = $product->image ?? '';
    
    $_SESSION['cart'][$product_id] = [
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'qty' => $quantity,
        'image' => $image
    ];
}

$_SESSION['success'] = "Product added to cart successfully";
header("Location: cart.php");
exit;
?>