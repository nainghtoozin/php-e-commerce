<?php
include(__DIR__ . '/../vendor/autoload.php');

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;
use Libs\Database\CategoriesTable;
use Libs\Database\UsersTable;
use Libs\Database\OrdersTable;
use Helpers\Auth;

$auth = Auth::check();

$productsTable = new ProductsTable(new MySQL());
$categoriesTable = new CategoriesTable(new MySQL());
$usersTable = new UsersTable(new MySQL());
$ordersTable = new OrdersTable(new MySQL());

// Get statistics
$totalProducts = $productsTable->countAll();
$totalCategories = $categoriesTable->countAll();
$totalUsers = $usersTable->countAll();
$totalOrders = $ordersTable->countAll();
$totalRevenue = $ordersTable->getTotalRevenue();
$recentOrdersCount = $ordersTable->getRecentOrdersCount(7);

// Get latest data
$recentProducts = $productsTable->getLatest(5);
$recentOrders = $ordersTable->getLatest(10);

$title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-2">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Products</h5>
                <p class="card-text fs-4"><?= $totalProducts ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Categories</h5>
                <p class="card-text fs-4"><?= $totalCategories ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Users</h5>
                <p class="card-text fs-4"><?= $totalUsers ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Orders</h5>
                <p class="card-text fs-4"><?= $totalOrders ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title">Revenue</h5>
                <p class="card-text fs-4">$<?= number_format($totalRevenue, 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-dark h-100">
            <div class="card-body">
                <h5 class="card-title">7 Days</h5>
                <p class="card-text fs-4"><?= $recentOrdersCount ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Products Table -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Products</h5>
        <a href="products/index.php" class="btn btn-sm btn-primary">View All Products</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentProducts)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No products found. <a href="products/create.php">Create your first product</a></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentProducts as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product->image): ?>
                                        <img src="../public/uploads/products/<?= htmlspecialchars($product->image) ?>"
                                            alt="<?= htmlspecialchars($product->name) ?>"
                                            class="rounded border"
                                            width="48"
                                            height="48"
                                            style="object-fit:cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary-subtle text-muted rounded d-flex align-items-center justify-content-center"
                                            style="width:48px;height:48px;">
                                            <small>No Image</small>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($product->name) ?></strong></td>
                                <td><?= htmlspecialchars($product->category) ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($product->sku) ?></small></td>
                                <td>
                                    <?php if ($product->quantity > 0): ?>
                                        <span class="badge bg-success"><?= $product->quantity ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?= number_format($product->price, 2) ?></td>
                                <td>
                                    <?php if ($product->status == 1): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="products/edit.php?id=<?= $product->id ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="products/view.php?id=<?= $product->id ?>" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Orders</h5>
        <small class="text-muted">Latest 10 orders</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No orders found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>
                                    <strong>#<?= str_pad($order->id, 6, '0', STR_PAD_LEFT) ?></strong>
                                    <?php if ($order->item_count > 1): ?>
                                        <small class="text-muted d-block">(<?= $order->item_count ?> items)</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($order->customer_name) ?></td>
                                <td><?= htmlspecialchars($order->phone) ?></td>
                                <td><strong>$<?= number_format($order->total_amount, 2) ?></strong></td>
                                <td>
                                    <?php if ($order->status === 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($order->status === 'completed'): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php elseif ($order->status === 'cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= ucfirst($order->status) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('M j, Y H:i', strtotime($order->created_at)) ?>
                                    </small>
                                </td>
                                <td>
                                    <a href="admin/order_view.php?id=<?= $order->id ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>