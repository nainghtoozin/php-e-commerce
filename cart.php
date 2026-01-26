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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Commerce Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .product-image {
            height: 80px;
            object-fit: cover;
            width: 80px;
        }
        
        .quantity-input {
            width: 80px;
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
                        <a class="nav-link" href="index.php#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($_SESSION['cart'])): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">Your cart is empty</h4>
                                <p class="text-muted">Add some products to your cart to get started</p>
                                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($_SESSION['cart'] as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['image']): ?>
                                                        <img src="public/uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                                             class="product-image me-3 rounded">
                                                    <?php else: ?>
                                                        <div class="product-image bg-secondary d-flex align-items-center justify-content-center me-3 rounded">
                                                            <i class="bi bi-image text-white"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                                        <small class="text-muted">ID: <?= $item['id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$<?= number_format($item['price'], 2) ?></td>
                                            <td>
                                                <form action="cart_update.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                    <input type="number" name="quantity" value="<?= $item['qty'] ?>" 
                                                           min="1" max="10" class="form-control quantity-input d-inline-block">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-1">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>$<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                            <td>
                                                <form action="cart_remove.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Remove this item from cart?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <?php if (!empty($_SESSION['cart'])): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>
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
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total:</h5>
                                <h5>$<?= number_format($total_amount, 2) ?></h5>
                            </div>
                            <a href="checkout.php" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-credit-card"></i> Proceed to Checkout
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="bi bi-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
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