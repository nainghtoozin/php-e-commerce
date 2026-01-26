<?php
include(__DIR__ . '/../../vendor/autoload.php');

use Libs\Database\MySQL;
use Libs\Database\OrdersTable;
use Helpers\Auth;

// Check authentication
$auth = Auth::check();

// Get order ID from URL
$order_id = $_GET['id'] ?? null;

if (!$order_id || !is_numeric($order_id)) {
    $_SESSION['error'] = "Invalid order ID";
    header("Location: ../dashboard.php");
    exit;
}

// Initialize orders table
$ordersTable = new OrdersTable(new MySQL());

// Get order details
$order = $ordersTable->findById($order_id);

if (!$order) {
    $_SESSION['error'] = "Order not found";
    header("Location: ../dashboard.php");
    exit;
}

$title = 'Order Details - #' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- Order Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Order Details</h2>
        <p class="text-muted mb-0">Order #<?= str_pad($order->id, 6, '0', STR_PAD_LEFT) ?></p>
    </div>
    <div>
        <a href="../dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
        <a href="../_actions/orders/index.php" class="btn btn-outline-primary">
            <i class="bi bi-list"></i> All Orders
        </a>
    </div>
</div>

<!-- Order Information -->
<div class="row">
    <!-- Customer Information -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Name</label>
                    <p class="mb-1 fw-bold"><?= htmlspecialchars($order->customer_name) ?></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Phone</label>
                    <p class="mb-1 fw-bold"><?= htmlspecialchars($order->phone) ?></p>
                </div>
                <div>
                    <label class="text-muted small">Delivery Address</label>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($order->address)) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Order Date</label>
                    <p class="mb-1 fw-bold"><?= date('F j, Y - g:i A', strtotime($order->created_at)) ?></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Status</label>
                    <div class="mb-1">
                        <?php if ($order->status === 'pending'): ?>
                            <span class="badge bg-warning fs-6">Pending</span>
                        <?php elseif ($order->status === 'completed'): ?>
                            <span class="badge bg-success fs-6">Completed</span>
                        <?php elseif ($order->status === 'cancelled'): ?>
                            <span class="badge bg-danger fs-6">Cancelled</span>
                        <?php else: ?>
                            <span class="badge bg-secondary fs-6"><?= ucfirst($order->status) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Payment Method</label>
                    <p class="mb-1 fw-bold">Cash on Delivery (COD)</p>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <h5 class="mb-0">Total Amount:</h5>
                    <h4 class="mb-0 text-primary">$<?= number_format($order->total_amount, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Order Items</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($order->items)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No items found in this order.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($order->items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($item->product_name) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($item->sku) ?></small>
                                </td>
                                <td>$<?= number_format($item->price, 2) ?></td>
                                <td><?= $item->quantity ?></td>
                                <td><strong>$<?= number_format($item->subtotal, 2) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Total:</td>
                        <td class="fw-bold text-primary">$<?= number_format($order->total_amount, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="mt-4 d-flex gap-2">
    <button onclick="window.print()" class="btn btn-outline-primary">
        <i class="bi bi-printer"></i> Print Order
    </button>
    <button onclick="history.back()" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </button>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>