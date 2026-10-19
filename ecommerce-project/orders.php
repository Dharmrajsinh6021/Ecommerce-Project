<?php
require_once __DIR__ . '/config/db.php';

if (!is_logged_in()) {
    header("Location: login.php?redirect=orders.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Fetch customer's orders along with item counts
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.id) AS product_count 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// If user requested specific order items via AJAX or GET for modal
$selected_order_items = [];
$selected_order = null;
if (isset($_GET['view_order'])) {
    $v_id = (int)$_GET['view_order'];
    $o_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $o_stmt->execute([$v_id, $user_id]);
    $selected_order = $o_stmt->fetch();

    if ($selected_order) {
        $it_stmt = $pdo->prepare("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $it_stmt->execute([$v_id]);
        $selected_order_items = $it_stmt->fetchAll();
    }
}

$page_title = "My Orders";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">My Order History</h1>
        <p class="mb-0 text-white-50">Track, review, and manage all your past gadget purchases.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="list-group shadow-sm rounded-4 border-0 overflow-hidden">
                <a href="profile.php" class="list-group-item list-group-item-action py-3 fw-semibold">
                    <i class="fa fa-user me-2 text-muted"></i> Profile & Address
                </a>
                <a href="orders.php" class="list-group-item list-group-item-action active py-3 fw-semibold">
                    <i class="fa fa-box-open me-2"></i> Order History
                </a>
                <a href="cart.php" class="list-group-item list-group-item-action py-3 fw-semibold">
                    <i class="fa fa-shopping-cart me-2 text-muted"></i> Active Cart
                </a>
                <a href="logout.php" class="list-group-item list-group-item-action py-3 fw-semibold text-danger">
                    <i class="fa fa-arrow-right-from-bracket me-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="col-lg-9">
            <!-- Details Modal / Popover if order selected -->
            <?php if ($selected_order): ?>
                <div class="card border-primary border-2 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="fa fa-receipt me-2"></i>Details for Order #<?= $selected_order['id'] ?>
                        </h5>
                        <a href="orders.php" class="btn btn-sm btn-outline-secondary rounded-pill">&times; Close View</a>
                    </div>

                    <div class="row g-3 mb-3 small">
                        <div class="col-md-4">
                            <strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($selected_order['created_at'])) ?><br>
                            <strong>Status:</strong> <span class="badge bg-primary"><?= htmlspecialchars($selected_order['order_status']) ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Payment Method:</strong> <?= htmlspecialchars($selected_order['payment_method']) ?><br>
                            <strong>Payment Status:</strong> <?= htmlspecialchars($selected_order['payment_status']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Delivery Address:</strong><br>
                            <?= htmlspecialchars($selected_order['shipping_address']) ?>, <?= htmlspecialchars($selected_order['city']) ?> (<?= htmlspecialchars($selected_order['pincode']) ?>)
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Unit Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selected_order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="rounded-2 me-2 border" style="width: 35px; height: 35px; object-fit: contain;">
                                                <?php endif; ?>
                                                <span class="fw-semibold small"><?= htmlspecialchars($item['product_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="small"><?= format_price($item['price']) ?></td>
                                        <td class="text-center small"><?= $item['quantity'] ?></td>
                                        <td class="text-end fw-semibold small"><?= format_price($item['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Amount:</th>
                                    <th class="text-end text-primary"><?= format_price($selected_order['total_amount']) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0">Past Orders</h5>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-bag-shopping text-muted display-4 mb-3"></i>
                        <h5>No Orders Placed Yet</h5>
                        <p class="text-muted small">You haven't placed any orders so far.</p>
                        <a href="products.php" class="btn btn-primary rounded-pill btn-sm px-4">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4">Order ID</th>
                                    <th>Date</th>
                                    <th>Products</th>
                                    <th>Total Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        <td class="small text-muted"><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                                        <td class="small"><?= $ord['product_count'] ?> item(s)</td>
                                        <td class="fw-bold"><?= format_price($ord['total_amount']) ?></td>
                                        <td class="small"><?= htmlspecialchars($ord['payment_method']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $badge_color ?> rounded-pill px-2 py-1">
                                                <?= htmlspecialchars($ord['order_status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="orders.php?view_order=<?= $ord['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
