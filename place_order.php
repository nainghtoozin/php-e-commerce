<?php
session_start();
include("vendor/autoload.php");

use Libs\Database\MySQL;

// Redirect if no order data or cart is empty
if (!isset($_SESSION['order_data']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$mysql = new MySQL();
$db = $mysql->connect();

try {
    // Start transaction
    $db->beginTransaction();
    
    $order_data = $_SESSION['order_data'];
    $cart_items = $_SESSION['cart'];
    
    // Calculate total on server (never trust client)
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['qty'];
    }
    
    // Insert order
    $stmt = $db->prepare("
        INSERT INTO orders (customer_name, phone, address, total_amount, status) 
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $order_data['customer_name'],
        $order_data['phone'],
        $order_data['address'],
        $total_amount
    ]);
    
    $order_id = $db->lastInsertId();
    
    // Insert order items
    $stmt = $db->prepare("
        INSERT INTO order_items (order_id, product_id, price, quantity, subtotal) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach ($cart_items as $item) {
        $subtotal = $item['price'] * $item['qty'];
        $stmt->execute([
            $order_id,
            $item['id'],
            $item['price'],
            $item['qty'],
            $subtotal
        ]);
        
        // Update product stock in inventories table
        $update_stmt = $db->prepare("
            UPDATE inventories 
            SET quantity = quantity - ? 
            WHERE product_id = ? AND quantity >= ?
        ");
        $update_stmt->execute([$item['qty'], $item['id'], $item['qty']]);
        
        if ($update_stmt->rowCount() === 0) {
            throw new Exception("Product #{$item['id']} is out of stock");
        }
    }
    
    // Commit transaction
    $db->commit();
    
    // Store order ID for success page and clear cart
    $_SESSION['order_id'] = $order_id;
    unset($_SESSION['cart']);
    unset($_SESSION['order_data']);
    unset($_SESSION['form_data']);
    
    // Redirect to success page
    header("Location: order_success.php");
    exit;
    
} catch (Exception $e) {
    // Rollback transaction
    $db->rollBack();
    
    $_SESSION['error'] = "Failed to place order: " . $e->getMessage();
    header("Location: checkout.php");
    exit;
}
?>