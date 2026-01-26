<?php

use Libs\Database\ProductsTable;
use Libs\Database\MySQL;
use Helpers\Auth;

include("../../vendor/autoload.php");

$auth = Auth::check();

$id = $_GET['id'] ?? null;
if (!$id) exit;

$table = new ProductsTable(new MySQL());
$product = $table->findById($id);

if (!$product) exit;
?>

<div class="modal-header">
    <h5 class="modal-title"><?= htmlspecialchars($product->name) ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    <!-- Main Image -->
    <?php if (!empty($product->images)): ?>
        <?php foreach ($product->images as $img): ?>
            <?php if ($img->is_primary): ?>
                <img src="../../public/uploads/products/<?= $img->image ?>"
                    class="img-fluid rounded mb-3 border object-fit" alt="<?= $product->name ?>" width="200px" height="100px">
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><strong>SKU:</strong> <?= $product->sku ?></p>
    <p><strong>Price:</strong> $<?= number_format($product->price, 2) ?></p>
    <p><strong>Short Description:</strong></p>
    <p><?= nl2br(htmlspecialchars($product->short_description)) ?></p>
    <p><strong>Description:</strong></p>
    <p><?= nl2br(htmlspecialchars($product->description)) ?></p>

    <!-- Gallery -->
    <div class="row g-2">
        <?php foreach ($product->images as $img): ?>
            <?php if (!$img->is_primary): ?>
                <div class="col-3">
                    <img src="../../public/uploads/products/<?= $img->image ?>"
                        class="img-fluid rounded border">
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">
        Close
    </button>
</div>