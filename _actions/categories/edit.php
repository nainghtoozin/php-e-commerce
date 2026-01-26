<?php

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;
use Helpers\Auth;

include("../../vendor/autoload.php");

$auth = Auth::check();

$id = $_GET['id'];
$table = new CategoriesTable(new MySQL());
$category = $table->getById($id);
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

    <div class="container py-5">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">➕ Edit Category</h3>
            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <!-- Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <form action="update.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <!-- Category Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text"
                            name="name"
                            class="form-control"
                            placeholder="Enter category name"
                            value="<?= $category->name ?>"
                            required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description"
                            rows="4"
                            class="form-control"
                            placeholder="Enter category description">
                            <?= htmlspecialchars($category->description) ?>
                        </textarea>
                    </div>

                    <!-- Image -->
                    <div class="mb-4">
                        <?php if ($category->image): ?>
                            <div class="mt-2">
                                <img src="../../public/uploads/categories/<?= $category->image ?>"
                                    width="80"
                                    class="rounded border">
                            </div>
                        <?php endif; ?>
                        <label class="form-label fw-semibold">Category Image</label>
                        <input type="file"
                            name="image"
                            class="form-control"
                            accept="image/*">
                        <small class="text-muted">
                            JPG, PNG, WEBP recommended
                        </small>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary">
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Updated Category
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>