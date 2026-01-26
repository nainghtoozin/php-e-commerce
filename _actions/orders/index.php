<?php
include(__DIR__ . '/../../vendor/autoload.php');

use Libs\Database\MySQL;
use Libs\Database\OrdersTable;
use Helpers\Auth;

// Check authentication
$auth = Auth::check();

// Initialize orders table
$ordersTable = new OrdersTable(new MySQL());

// Get all orders (latest first)
$orders = $ordersTable->getAll();

$title = 'Orders Management';
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Orders Management</h2>
    <div class="text-muted">
        Total Orders: <?= count($orders) ?>
    </div>
</div>

<!-- Orders Table -->
<div class="card shadow-sm">
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
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-cart-x" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                <h5>No orders found</h5>
                                <p>When customers place orders, they will appear here.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
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
                                    <a href="../admin/order_view.php?id=<?= $order->id ?>" class="btn btn-sm btn-primary">
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
require_once __DIR__ . '/../../includes/footer.php';
?>