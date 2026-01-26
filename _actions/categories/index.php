<?php
include("../../vendor/autoload.php");

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;
use Helpers\Auth;

$auth = Auth::check();

$table = new CategoriesTable(new MySQL());
$categories = $table->getAll();
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

    <div class="container py-5">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">📂 Categories
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Category
                </a>
            </h3>
            <a href="../dashboard.php" class="btn btn-success ms-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="bg-light border-bottom">
                            <tr class="text-uppercase text-muted small">
                                <th style="width:40px;">#</th>
                                <th style="width:70px;">Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th style="width:120px;">Created</th>
                                <th style="width:120px;">Updated</th>
                                <th style="width:140px;" class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white shadow-sm rounded-3">

                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $index => $category): ?>
                                    <tr class="border-bottom">

                                        <!-- ID -->
                                        <td class="text-muted">
                                            <?= $index + 1 ?>
                                        </td>

                                        <!-- Image -->
                                        <td>
                                            <?php if ($category->image): ?>
                                                <img
                                                    src="../../public/uploads/categories/<?= $category->image ?>"
                                                    alt="<?= htmlspecialchars($category->name) ?>"
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

                                        <!-- Name -->
                                        <td>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($category->name) ?>
                                            </div>
                                        </td>

                                        <!-- Description -->
                                        <td>
                                            <div class="text-muted small" style="max-width:280px;">
                                                <?= htmlspecialchars(mb_strimwidth($category->description, 0, 80, '...')) ?>
                                            </div>
                                        </td>

                                        <!-- Created -->
                                        <td class="small text-muted">
                                            <?= date('d M Y', strtotime($category->created_at)) ?>
                                        </td>

                                        <!-- Updated -->
                                        <td class="small text-muted">
                                            <?= $category->updated_at
                                                ? date('d M Y', strtotime($category->updated_at))
                                                : '-' ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit.php?id=<?= $category->id ?>"
                                                    class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <a href="delete.php?id=<?= $category->id ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('Delete this category?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                        No categories found
                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>