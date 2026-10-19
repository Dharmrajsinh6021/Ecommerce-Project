<?php
$admin_page_title = "Customer Orders Management";
require_once __DIR__ . '/includes/header.php';

$message = '';
$message_type = 'success';

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id       = (int)($_POST['order_id'] ?? 0);
    $order_status   = clean($_POST['order_status'] ?? 'Pending');
    $payment_status = clean($_POST['payment_status'] ?? 'Pending');

    if ($order_id > 0) {
        $up_stmt = $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?");
        $up_stmt->execute([$order_status, $payment_status, $order_id]);
        $message = "Order #$order_id status updated to '$order_status' ($payment_status)!";
    }
}

// Fetch all orders
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();

// View order details modal support
$view_order = null;
$view_items = [];
if (isset($_GET['view'])) {
    $v_id = (int)$_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$v_id]);
    $view_order = $stmt->fetch();

    if ($view_order) {
        $it_stmt = $pdo->prepare("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $it_stmt->execute([$v_id]);
        $view_items = $it_stmt->fetchAll();
    }
}
?>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show rounded-4" role="alert">
        <i class="fa fa-info-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Selected Order Inspection Panel -->
<?php if ($view_order): ?>
    <div class="card border-primary border-2 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h5 class="fw-bold text-primary mb-0">
                <i class="fa fa-file-invoice me-2"></i>Details for Order #<?= $view_order['id'] ?>
            </h5>
            <a href="orders.php" class="btn btn-sm btn-outline-secondary rounded-pill">&times; Dismiss</a>
        </div>

        <div class="row g-3 mb-3 small">
            <div class="col-md-4">
                <p class="mb-1"><strong>Customer Name:</strong> <?= htmlspecialchars($view_order['customer_name']) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($view_order['customer_email']) ?></p>
                <p class="mb-0"><strong>Mobile:</strong> <?= htmlspecialchars($view_order['customer_mobile']) ?></p>
            </div>
            <div class="col-md-4">
                <p class="mb-1"><strong>Order Placed:</strong> <?= date('d M Y, h:i A', strtotime($view_order['created_at'])) ?></p>
                <p class="mb-1"><strong>Payment Mode:</strong> <?= htmlspecialchars($view_order['payment_method']) ?></p>
                <p class="mb-0"><strong>Payment Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($view_order['payment_status']) ?></span></p>
            </div>
            <div class="col-md-4">
                <p class="mb-1"><strong>Shipping Destination:</strong></p>
                <p class="mb-0 text-muted"><?= htmlspecialchars($view_order['shipping_address']) ?>, <?= htmlspecialchars($view_order['city']) ?>, <?= htmlspecialchars($view_order['state']) ?> - <?= htmlspecialchars($view_order['pincode']) ?></p>
            </div>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($view_items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="rounded-2 me-2 border" style="width: 35px; height: 35px; object-fit: contain;">
                                    <?php endif; ?>
                                    <span class="small fw-semibold"><?= htmlspecialchars($item['product_name']) ?></span>
                                </div>
                            </td>
                            <td class="small"><?= format_price($item['price']) ?></td>
                            <td class="text-center small"><?= $item['quantity'] ?></td>
                            <td class="text-end fw-bold small"><?= format_price($item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Grand Total:</th>
                        <th class="text-end text-primary fs-5"><?= format_price($view_order['total_amount']) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Quick Status Update Form -->
        <form method="POST" action="orders.php" class="bg-light p-3 rounded-4 border">
            <input type="hidden" name="update_status" value="1">
            <input type="hidden" name="order_id" value="<?= $view_order['id'] ?>">

            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="small fw-bold">Update Order Status:</label>
                </div>
                <div class="col-auto">
                    <select name="order_status" class="form-select form-select-sm">
                        <?php foreach (['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'] as $st): ?>
                            <option value="<?= $st ?>" <?= $view_order['order_status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="payment_status" class="form-select form-select-sm">
                        <?php foreach (['Pending', 'Paid', 'Failed'] as $pst): ?>
                            <option value="<?= $pst ?>" <?= $view_order['payment_status'] === $pst ? 'selected' : '' ?>><?= $pst ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Save Status</button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Orders Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="fw-bold mb-0">Customer Orders (<?= count($orders) ?>)</h5>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-4">Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">No orders found.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $ord): ?>
                        <?php
                        $badge_color = match($ord['order_status']) {
                            'Delivered'  => 'success',
                            'Shipped'    => 'info',
                            'Processing' => 'warning text-dark',
                            'Cancelled'  => 'danger',
                            default      => 'secondary'
                        };
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#<?= $ord['id'] ?></td>
                            <td>
                                <div class="fw-semibold text-dark small"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($ord['customer_mobile']) ?></div>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($ord['created_at'])) ?></td>
                            <td class="fw-bold"><?= format_price($ord['total_amount']) ?></td>
                            <td class="small"><?= htmlspecialchars($ord['payment_method']) ?></td>
                            <td>
                                <span class="badge bg-<?= $ord['payment_status'] === 'Paid' ? 'success' : 'secondary' ?> rounded-pill">
                                    <?= htmlspecialchars($ord['payment_status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $badge_color ?> rounded-pill">
                                    <?= htmlspecialchars($ord['order_status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="orders.php?view=<?= $ord['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fa fa-eye me-1"></i> View / Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
