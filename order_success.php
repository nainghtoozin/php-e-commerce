<?php
session_start();

// Redirect if no order ID
if (!isset($_SESSION['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = $_SESSION['order_id'];
unset($_SESSION['order_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - E-Commerce Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
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
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="success-icon mb-4">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h1 class="mb-3">Order Placed Successfully!</h1>
                        <p class="lead mb-4">
                            Thank you for your order. Your order has been received and is being processed.
                        </p>
                        
                        <div class="alert alert-success mb-4">
                            <strong>Order Number:</strong> #<?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?>
                        </div>
                        
                        <div class="bg-light p-4 rounded mb-4 text-start">
                            <h5 class="mb-3">Order Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Payment Method:</strong> Cash on Delivery (COD)
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Status:</strong> <span class="badge bg-warning">Pending</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 mb-2">
                                    <strong>Delivery:</strong> Standard Delivery
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Expected:</strong> 3-5 business days
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle"></i>
                            <strong>Important:</strong> You will pay when your order is delivered. 
                            Please keep the exact amount ready for the delivery person.
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="index.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-house"></i> Continue Shopping
                            </a>
                            <button onclick="window.print()" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-printer"></i> Print Order
                            </button>
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