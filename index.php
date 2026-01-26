<?php
include("vendor/autoload.php");

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;
use Libs\Database\CategoriesTable;

$productsTable = new ProductsTable(new MySQL());
$categoriesTable = new CategoriesTable(new MySQL());

// Get latest 8 products (active and in-stock only)
$allLatest = $productsTable->getLatest(8);
$products = array_filter($allLatest, function ($p) {
    return $p->status == 1 && $p->quantity > 0; // Only active and in-stock products
});

// Get all categories
$categories = $categoriesTable->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - E-Commerce Store</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }

        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }

        .category-card {
            transition: transform 0.3s;
            cursor: pointer;
        }

        .category-card:hover {
            transform: scale(1.05);
        }

        .category-image {
            height: 150px;
            object-fit: cover;
            width: 100%;
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Categories</a>
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

    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Welcome to Our Store</h1>
            <p class="lead mb-4">Discover amazing products at great prices</p>
            <a href="#products" class="btn btn-light btn-lg">Shop Now</a>
        </div>
    </div>

    <div class="container">
        <!-- Categories Section -->
        <?php if (!empty($categories)): ?>
            <section id="categories" class="mb-5">
                <h2 class="mb-4">Shop by Category</h2>
                <div class="row g-4">
                    <?php foreach ($categories as $category): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card category-card shadow-sm">
                                <?php if ($category->image): ?>
                                    <img src="public/uploads/categories/<?= htmlspecialchars($category->image) ?>"
                                        alt="<?= htmlspecialchars($category->name) ?>"
                                        class="card-img-top category-image">
                                <?php else: ?>
                                    <div class="card-img-top category-image bg-secondary d-flex align-items-center justify-content-center">
                                        <i class="bi bi-folder text-white" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= htmlspecialchars($category->name) ?></h5>
                                    <?php if ($category->description): ?>
                                        <p class="card-text text-muted small"><?= htmlspecialchars(substr($category->description, 0, 60)) ?>...</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Products Section -->
        <section id="products" class="mb-5">
            <h2 class="mb-4">Latest Products</h2>
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No products available at the moment. Please check back later.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-3">
                            <a href="product.php?id=<?= $product->id ?>" class="text-decoration-none text-dark">
                                <div class="card product-card shadow-sm h-100">
                                    <?php if ($product->image): ?>
                                        <img src="public/uploads/products/<?= htmlspecialchars($product->image) ?>"
                                            alt="<?= htmlspecialchars($product->name) ?>"
                                            class="card-img-top product-image">
                                    <?php else: ?>
                                        <div class="card-img-top product-image bg-secondary d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image text-white" style="font-size: 4rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= htmlspecialchars($product->name) ?></h5>
                                        <div class="mt-auto">
                                            <span class="h5 text-primary mb-0">$<?= number_format($product->price, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
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