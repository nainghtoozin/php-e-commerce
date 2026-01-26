<?php
include("vendor/autoload.php");

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

$productsTable = new ProductsTable(new MySQL());
$product = $productsTable->findById($id);

if (!$product || $product->status != 1) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product->name) ?> - E-Commerce Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .product-image-main {
            max-height: 500px;
            object-fit: contain;
            width: 100%;
        }

        .product-image-thumb {
            cursor: pointer;
            transition: opacity 0.3s;
            opacity: 0.7;
        }

        .product-image-thumb:hover,
        .product-image-thumb.active {
            opacity: 1;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link" href="index.php#categories">Categories</a>
                    </li>
<li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart"></i> Cart
                            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                <span class="badge bg-danger"><?= count($_SESSION['cart']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php#products">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product->name) ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6 mb-4">
                <?php if (!empty($product->images)): ?>
                    <?php 
                    $primaryImage = null;
                    $otherImages = [];
                    foreach ($product->images as $img) {
                        if ($img->is_primary == 1) {
                            $primaryImage = $img;
                        } else {
                            $otherImages[] = $img;
                        }
                    }
                    if (!$primaryImage && !empty($product->images)) {
                        $primaryImage = $product->images[0];
                    }
                    ?>
                    
                    <!-- Main Image -->
                    <div class="mb-3">
                        <img id="mainImage" 
                             src="public/uploads/products/<?= htmlspecialchars($primaryImage->image) ?>" 
                             alt="<?= htmlspecialchars($product->name) ?>" 
                             class="product-image-main border rounded">
                    </div>

                    <!-- Thumbnail Gallery -->
                    <?php if (!empty($otherImages)): ?>
                    <div class="row g-2">
                        <?php foreach ($otherImages as $thumb): ?>
                        <div class="col-3">
                            <img src="public/uploads/products/<?= htmlspecialchars($thumb->image) ?>" 
                                 alt="Thumbnail" 
                                 class="img-fluid product-image-thumb border rounded"
                                 onclick="changeImage('public/uploads/products/<?= htmlspecialchars($thumb->image) ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="bg-secondary d-flex align-items-center justify-content-center border rounded" style="height: 500px;">
                        <i class="bi bi-image text-white" style="font-size: 5rem;"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <h1 class="mb-3"><?= htmlspecialchars($product->name) ?></h1>
                
                <div class="mb-3">
                    <span class="badge bg-secondary"><?= htmlspecialchars($product->category ?? 'Uncategorized') ?></span>
                    <span class="badge bg-info">SKU: <?= htmlspecialchars($product->sku) ?></span>
                </div>

                <div class="mb-4">
                    <h2 class="text-primary mb-0">$<?= number_format($product->price, 2) ?></h2>
                </div>

                <?php if ($product->short_description): ?>
                <div class="mb-4">
                    <p class="lead"><?= htmlspecialchars($product->short_description) ?></p>
                </div>
                <?php endif; ?>

                <div class="mb-4">
<?php 
$quantity = $product->quantity ?? 0;
if ($quantity > 0): ?>
                        <span class="badge bg-success fs-6 mb-3">In Stock (<?= $quantity ?> available)</span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6 mb-3">Out of Stock</span>
                    <?php endif; ?>
                </div>

<div class="mb-4">
                    <?php 
                    $quantity = $product->quantity ?? 0;
                    if ($quantity > 0): ?>
                        <form action="cart_add.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product->id ?>">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Quantity:</span>
                                <input type="number" name="quantity" class="form-control" 
                                       value="1" min="1" max="<?= min(10, $quantity) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="bi bi-cart-x"></i> Out of Stock
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($product->description): ?>
                <div class="mt-5">
                    <h3>Description</h3>
                    <hr>
                    <div class="text-muted">
                        <?= nl2br(htmlspecialchars($product->description)) ?>
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
    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
            // Update active thumbnail
            document.querySelectorAll('.product-image-thumb').forEach(thumb => {
                thumb.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>

</html>
