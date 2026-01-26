<?php

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;
use Helpers\Auth;

include("../../vendor/autoload.php");

$auth = Auth::check();

$table = new CategoriesTable(new MySQL());
$categories = $table->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Category</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Create Product</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="store.php" enctype="multipart/form-data">

                    <div class="row g-3">

                        <!-- Product Name -->
                        <div class="col-md-6">
                            <label class="form-label">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="name"
                                name="name"
                                class="form-control"
                                placeholder="Enter product name"
                                required>
                            <small class="text-muted">
                                This will be used to generate product slug
                            </small>
                        </div>

                        <!-- SKU -->
                        <div class="col-md-6">
                            <label class="form-label">
                                SKU <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="sku"
                                name="sku"
                                class="form-control"
                                placeholder="Auto generated"
                                required>
                            <small class="text-muted">
                                SKU is auto generated. You can edit it if needed.
                            </small>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <label class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category_id"
                                class="form-select"
                                required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c->id ?>">
                                        <?= $c->name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="col-md-6">
                            <label class="form-label">
                                Price <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                step="0.01"
                                name="price"
                                class="form-control"
                                placeholder="0.00"
                                required>
                        </div>

                        <!-- Short Description -->
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description"
                                class="form-control"
                                rows="2"
                                placeholder="Short summary for product list"></textarea>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                class="form-control"
                                rows="5"
                                placeholder="Full product description"></textarea>
                        </div>

                        <!-- Images -->
                        <div class="col-12">
                            <label class="form-label">
                                Product Images
                            </label>
                            <input type="file"
                                name="images[]"
                                class="form-control"
                                multiple>
                            <small class="text-muted">
                                First image will be used as main image
                            </small>
                        </div>

                        <!-- Stock Quantity -->
                        <div class="col-md-6">
                            <label class="form-label">
                                Stock Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                name="quantity"
                                class="form-control"
                                min="0"
                                placeholder="Enter available stock"
                                required>
                            <small class="text-muted">
                                How many items available in inventory
                            </small>
                        </div>


                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status"
                                class="form-select">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-4 text-end">
                        <a href="index.php" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button class="btn btn-primary">
                            Save Product
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('name').addEventListener('input', function() {
            let name = this.value.toUpperCase().replace(/\s+/g, '-');
            let random = Math.floor(1000 + Math.random() * 9000);
            document.getElementById('sku').value = name.substring(0, 10) + '-' + random;
        });
    </script>

</body>

</html>