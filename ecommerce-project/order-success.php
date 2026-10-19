<?php
require_once __DIR__ . '/config/db.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0 || !is_logged_in()) {
    header("Location: index.php");
    exit;
}

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

// Fetch order items
$items_stmt = $pdo->prepare("
    SELECT oi.*, p.image 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll();

$page_title = "Order Placed Successfully #" . $order['id'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Congratulatory Card -->
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5 mb-4 bg-white">
                <div class="mb-3">
                    <span class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px;">
                        <i class="fa fa-check fs-1"></i>
                    </span>
                </div>
                <h2 class="fw-bold text-dark mb-1">Thank You! Order Confirmed</h2>
                <p class="text-muted mb-3">Your order has been placed and is currently being processed by our warehouse.</p>
                
                <div class="d-inline-flex align-items-center justify-content-center gap-2 bg-light px-4 py-2 rounded-pill mx-auto mb-4 border">
                    <span class="text-muted">Order ID:</span>
                    <strong class="text-primary fs-5">#<?= $order['id'] ?></strong>
                    <span class="text-muted ms-3">Placed on:</span>
                    <strong><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></strong>
                </div>

                <!-- Order Receipt Details -->
                <div class="text-start border-top pt-4">
                    <h5 class="fw-bold mb-3"><i class="fa fa-receipt text-primary me-2"></i>Order Invoice Breakdown</h5>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($it['image'])): ?>
                                                    <img src="<?= htmlspecialchars($it['image']) ?>" alt="" class="rounded-2 me-2 border" style="width: 40px; height: 40px; object-fit: contain;">
                                                <?php endif; ?>
                                                <span class="fw-semibold small"><?= htmlspecialchars($it['product_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="small"><?= format_price($it['price']) ?></td>
                                        <td class="text-center small"><?= $it['quantity'] ?></td>
                                        <td class="text-end fw-semibold small"><?= format_price($it['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Amount:</th>
                                    <th class="text-end text-primary fs-5"><?= format_price($order['total_amount']) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Customer & Shipping Summary Grid -->
                    <div class="row g-3 bg-light p-3 rounded-4 border mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold small text-uppercase text-secondary mb-2">Customer Details</h6>
                            <p class="small mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p class="small mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                            <p class="small mb-0"><strong>Mobile:</strong> <?= htmlspecialchars($order['customer_mobile']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold small text-uppercase text-secondary mb-2">Delivery & Payment</h6>
                            <p class="small mb-1"><strong>Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['state']) ?> - <?= htmlspecialchars($order['pincode']) ?></p>
                            <p class="small mb-1"><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?> (<?= htmlspecialchars($order['payment_status']) ?>)</p>
                            <p class="small mb-0"><strong>Status:</strong> <span class="badge bg-warning text-dark"><?= htmlspecialchars($order['order_status']) ?></span></p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="products.php" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fa fa-shopping-bag me-1"></i> Continue Shopping
                        </a>
                        <a href="orders.php" class="btn btn-primary rounded-pill px-4">
                            <i class="fa fa-box me-1"></i> View Order History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
