<?php
include("../../vendor/autoload.php");

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;

$table = new ProductsTable(new MySQL());
$filters = [
    'q'          => $_GET['q'] ?? '',
    'status'     => $_GET['status'] ?? '',
    'stock'      => $_GET['stock'] ?? '',
    'price_from' => $_GET['price_from'] ?? '',
    'price_to'   => $_GET['price_to'] ?? '',
];
$hasFilter = false;
foreach ($filters as $value) {
    if ($value !== '') {
        $hasFilter = true;
        break;
    }
}

if ($hasFilter) {
    $products = $table->filter($filters);
} else {
    $products = $table->getAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Category Management</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <form method="GET" class="row g-2 mb-3">

            <!-- Search -->
            <div class="col-md-3">
                <input type="text"
                    name="q"
                    value="<?= $_GET['q'] ?? '' ?>"
                    class="form-control"
                    placeholder="Search name or SKU">
            </div>

            <!-- Status -->
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <!-- Stock -->
            <div class="col-md-2">
                <select name="stock" class="form-select">
                    <option value="">All Stock</option>
                    <option value="in" <?= ($_GET['stock'] ?? '') === 'in' ? 'selected' : '' ?>>In Stock</option>
                    <option value="out" <?= ($_GET['stock'] ?? '') === 'out' ? 'selected' : '' ?>>Out of Stock</option>
                </select>
            </div>

            <!-- Price From -->
            <div class="col-md-2">
                <input type="number"
                    name="price_from"
                    value="<?= $_GET['price_from'] ?? '' ?>"
                    class="form-control"
                    placeholder="Price from">
            </div>

            <!-- Price To -->
            <div class="col-md-2">
                <input type="number"
                    name="price_to"
                    value="<?= $_GET['price_to'] ?? '' ?>"
                    class="form-control"
                    placeholder="Price to">
            </div>

            <!-- Submit -->
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary">Filter</button>
            </div>
            <!-- Reset -->
            <div class="col-md-1 d-grid">
                <a href="index.php" class="btn btn-danger">Reset</a>
            </div>


        </form>


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Products</h4>
            <a href="create.php" class="btn btn-primary btn-sm">
                + Add Product
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- View Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" id="viewModalContent">
                    <!-- AJAX content will load here -->
                </div>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($products as $index => $p): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if ($p->image): ?>
                                    <img
                                        src="../../public/uploads/products/<?= $p->image ?>"
                                        alt="<?= htmlspecialchars($p->name) ?>"
                                        class="rounded border"
                                        width="48"
                                        height="48"
                                        style="object-fit:cover;">
                                <?php else: ?>
                                    <div class="bg-secondary-subtle text-muted rounded d-flex align-items-center justify-content-center"
                                        style="width:48px;height:48px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <strong><?= $p->name ?></strong><br>
                                <small class="text-muted"><?= $p->sku ?></small>
                            </td>

                            <td><?= $p->category ?></td>

                            <td>$<?= number_format($p->price, 2) ?></td>

                            <td>
                                <?php if ($p->quantity > 0): ?>
                                    <span class="badge bg-success">
                                        <?= $p->quantity ?> In Stock
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        Out of Stock
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($p->status): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">
                                <a href="#"
                                    class="btn btn-sm btn-info"
                                    onclick="viewProduct(<?= $p->id ?>)">
                                    View
                                </a>


                                <a href="edit.php?id=<?= $p->id ?>"
                                    class="btn btn-sm btn-warning">Edit</a>

                                <a href="delete.php?id=<?= $p->id ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this product?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewProduct(id) {
            fetch('view.php?id=' + id)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('viewModalContent').innerHTML = html;
                    let modal = new bootstrap.Modal(
                        document.getElementById('viewModal')
                    );
                    modal.show();
                });
        }
    </script>


</body>

</html>