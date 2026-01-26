<?php
session_start();
include("vendor/autoload.php");

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$productsTable = new ProductsTable(new MySQL());
$cart_items = $_SESSION['cart'];
$total_amount = 0;

// Validate all products still exist and are available
$mysql = new MySQL();
$db = $mysql->connect();

foreach ($cart_items as $product_id => $item) {
    $product = $productsTable->findById($product_id);
    if (!$product || $product->status != 1) {
        unset($_SESSION['cart'][$product_id]);
        continue;
    }
    
    // Check inventory stock
    $inv_stmt = $db->prepare("SELECT quantity FROM inventories WHERE product_id = ?");
    $inv_stmt->execute([$product_id]);
    $available_quantity = $inv_stmt->fetchColumn();
    
    if ($available_quantity === false || $available_quantity < $item['qty']) {
        $available = $available_quantity === false ? 0 : $available_quantity;
        if ($available > 0) {
            $_SESSION['cart'][$product_id]['qty'] = $available;
        } else {
            unset($_SESSION['cart'][$product_id]);
            continue;
        }
    }
    $total_amount += $item['price'] * $item['qty'];
}

// If cart is now empty after validation
if (empty($_SESSION['cart'])) {
    $_SESSION['error'] = "Some items in your cart are no longer available";
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    $errors = [];
    
    if (empty($customer_name)) {
        $errors[] = "Customer name is required";
    } elseif (strlen($customer_name) < 3) {
        $errors[] = "Customer name must be at least 3 characters";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
        $errors[] = "Please enter a valid phone number";
    }
    
    if (empty($address)) {
        $errors[] = "Delivery address is required";
    } elseif (strlen($address) < 10) {
        $errors[] = "Address must be at least 10 characters";
    }
    
    if (empty($errors)) {
        // Store order data in session for place_order.php
        $_SESSION['order_data'] = [
            'customer_name' => $customer_name,
            'phone' => $phone,
            'address' => $address,
            'total_amount' => $total_amount
        ];
        
        header("Location: place_order.php");
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-Commerce Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .product-image {
            height: 60px;
            object-fit: cover;
            width: 60px;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-shop"></i> E-Commerce Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="checkout.php">Checkout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">Checkout</h1>
        
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong><br>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <?= htmlspecialchars($error) ?><br>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Customer Information</h5>
                        <form method="POST" action="checkout.php">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="customer_name" 
                                       name="customer_name" required
                                       value="<?= htmlspecialchars($_SESSION['form_data']['customer_name'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" 
                                       name="phone" required
                                       value="<?= htmlspecialchars($_SESSION['form_data']['phone'] ?? '') ?>"
                                       placeholder="+1 (555) 123-4567">
                            </div>
                            
                            <div class="mb-4">
                                <label for="address" class="form-label">Delivery Address *</label>
                                <textarea class="form-control" id="address" name="address" 
                                          rows="3" required><?= htmlspecialchars($_SESSION['form_data']['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex">
                                <a href="cart.php" class="btn btn-outline-secondary me-3">
                                    <i class="bi bi-arrow-left"></i> Back to Cart
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-credit-card"></i> Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <hr>
                        <div class="mb-3">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="d-flex align-items-center mb-2">
                                <?php if ($item['image']): ?>
                                    <img src="public/uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="product-image me-2 rounded">
                                <?php else: ?>
                                    <div class="product-image bg-secondary d-flex align-items-center justify-content-center me-2 rounded">
                                        <i class="bi bi-image text-white"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <small class="fw-bold"><?= htmlspecialchars($item['name']) ?></small>
                                    <br>
                                    <small class="text-muted">Qty: <?= $item['qty'] ?> × $<?= number_format($item['price'], 2) ?></small>
                                </div>
                                <span class="fw-bold">$<?= number_format($item['price'] * $item['qty'], 2) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?= number_format($total_amount, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5>$<?= number_format($total_amount, 2) ?></h5>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                Cash on Delivery (COD) only. You'll pay when the order is delivered.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> E-Commerce Store. All rights reserved.</p>
            <p class="mb-0 mt-2">
                <a href="login.php" class="text-white-50">Admin Login</a>
            </p>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>