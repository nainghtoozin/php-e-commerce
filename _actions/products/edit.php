<?php

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;
use Libs\Database\ProductsTable;
use Helpers\Auth;

include("../../vendor/autoload.php");

$auth = Auth::check();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$productTable = new ProductsTable(new MySQL());
$categoryTable = new CategoriesTable(new MySQL());

$product    = $productTable->findById($id);
$categories = $categoryTable->getAll();

if (!$product) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-4">
        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Edit Product</h5>
            </div>

            <div class="card-body">
                <!-- 🔴 update.php -->
                <form method="POST" action="update.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product->id ?>">

                    <div class="row g-3">

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text"
                                name="name"
                                class="form-control"
                                value="<?= htmlspecialchars($product->name) ?>"
                                required>
                        </div>

                        <!-- SKU -->
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text"
                                name="sku"
                                class="form-control"
                                value="<?= $product->sku ?>"
                                required>
                            <small class="text-muted">
                                SKU is editable but should remain unique
                            </small>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c->id ?>"
                                        <?= $c->id == $product->category_id ? 'selected' : '' ?>>
                                        <?= $c->name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number"
                                step="0.01"
                                name="price"
                                class="form-control"
                                value="<?= $product->price ?>"
                                required>
                        </div>

                        <!-- Short Description -->
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description"
                                class="form-control"
                                rows="2"><?= htmlspecialchars($product->short_description) ?></textarea>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                class="form-control"
                                rows="5"><?= htmlspecialchars($product->description) ?></textarea>
                        </div>

                        <!-- Existing Images -->
                        <div class="col-12">
                            <label class="form-label">Existing Images</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach ($product->images as $img): ?>

                                    <img src="../../public/uploads/products/<?= $img->image ?>"
                                        width="80"
                                        class="rounded border">

                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Add New Images -->
                        <div class="col-12">
                            <label class="form-label">Add More Images</label>
                            <input type="file"
                                name="images[]"
                                class="form-control"
                                multiple>
                        </div>

                        <!-- Stock -->
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number"
                                name="quantity"
                                class="form-control"
                                min="0"
                                value="<?= $product->quantity ?>"
                                required>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" <?= $product->status == 1 ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $product->status == 0 ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-4 text-end">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-primary">Update Product</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>