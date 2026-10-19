<?php
$admin_page_title = "Admin Dashboard";
require_once __DIR__ . '/includes/header.php';

// 1. Total Users
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();

// 2. Total Categories
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// 3. Total Products
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// 4. Total Orders
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// 5. Total Sales/Revenue
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'Cancelled'")->fetchColumn();

// Recent Orders (Last 5)
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Auto-sync branding in contacts table if needed
try {
    $pdo->query("UPDATE contacts SET message = REPLACE(message, 'ElectroTech', 'TechTower') WHERE message LIKE '%ElectroTech%'");
} catch (Exception $e) {}

// Recent Contact Messages
$recent_inquiries = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 3")->fetchAll();
?>

<!-- Statistics Counter Row -->
<div class="row g-3 mb-4">
    <!-- Total Revenue -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card bg-primary text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-white-50 text-uppercase fw-bold">Total Sales</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= format_price($total_revenue) ?></h3>
                </div>
                <div class="rounded-circle bg-white-subtle p-3 fs-3 text-white">
                    <i class="fa fa-indian-rupee-sign"></i>
                </div>
            </div>
            <div class="small text-white-50 mt-2">
                <i class="fa fa-arrow-trend-up text-warning"></i> From completed orders
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card bg-success text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-white-50 text-uppercase fw-bold">Total Orders</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($total_orders) ?></h3>
                </div>
                <div class="rounded-circle bg-white-subtle p-3 fs-3 text-white">
                    <i class="fa fa-cart-shopping"></i>
                </div>
            </div>
            <div class="small text-white-50 mt-2">
                <a href="orders.php" class="text-white text-decoration-none">Manage orders &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card bg-info text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-white-50 text-uppercase fw-bold">Total Products</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($total_products) ?></h3>
                </div>
                <div class="rounded-circle bg-white-subtle p-3 fs-3 text-white">
                    <i class="fa fa-box-archive"></i>
                </div>
            </div>
            <div class="small text-white-50 mt-2">
                <a href="products.php" class="text-white text-decoration-none">Manage inventory &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Total Categories -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card bg-warning text-dark p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-muted text-uppercase fw-bold">Categories</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($total_categories) ?></h3>
                </div>
                <div class="rounded-circle bg-dark-subtle p-3 fs-3 text-dark">
                    <i class="fa fa-layer-group"></i>
                </div>
            </div>
            <div class="small text-muted mt-2">
                <a href="categories.php" class="text-dark fw-bold text-decoration-none">Manage categories &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card stat-card bg-dark text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="small text-white-50 text-uppercase fw-bold">Total Customers</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($total_users) ?></h3>
                </div>
                <div class="rounded-circle bg-white-subtle p-3 fs-3 text-white">
                    <i class="fa fa-users"></i>
                </div>
            </div>
            <div class="small text-white-50 mt-2">
                <a href="users.php" class="text-white text-decoration-none">View customers &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Row: Recent Orders and Quick Actions -->
<div class="row g-4">
    <!-- Recent Orders Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa fa-clock-rotate-left text-primary me-2"></i>Recent Customer Orders</h6>
                <a href="orders.php" class="btn btn-outline-primary btn-sm rounded-pill">View All Orders</a>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No orders found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $ord): ?>
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
                                    <td class="ps-3 fw-bold text-primary">#<?= $ord['id'] ?></td>
                                    <td>
                                        <div class="fw-semibold small"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($ord['customer_email']) ?></div>
                                    </td>
                                    <td class="fw-bold small"><?= format_price($ord['total_amount']) ?></td>
                                    <td class="small"><?= htmlspecialchars($ord['payment_method']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $badge_color ?> rounded-pill small">
                                            <?= htmlspecialchars($ord['order_status']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts & Inquiries -->
    <div class="col-lg-4">
        <!-- Quick Action Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Admin Shortcuts</h6>
            <div class="d-grid gap-2">
                <a href="products.php?action=create" class="btn btn-primary rounded-pill text-start py-2">
                    <i class="fa fa-plus-circle me-2"></i> Add New Product
                </a>
                <a href="categories.php" class="btn btn-outline-secondary rounded-pill text-start py-2">
                    <i class="fa fa-folder-plus me-2"></i> Manage Categories
                </a>
                <a href="orders.php" class="btn btn-outline-secondary rounded-pill text-start py-2">
                    <i class="fa fa-list-check me-2"></i> Review Pending Orders
                </a>
                <a href="../index.php" target="_blank" class="btn btn-outline-dark rounded-pill text-start py-2">
                    <i class="fa fa-store me-2"></i> Launch Customer Store
                </a>
            </div>
        </div>

        <!-- Latest Contact Submissions -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Customer Messages</h6>
            <?php if (empty($recent_inquiries)): ?>
                <p class="small text-muted mb-0">No new messages received.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_inquiries as $msg): ?>
                        <div class="list-group-item px-0 py-2">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <strong class="small"><?= htmlspecialchars($msg['name']) ?></strong>
                                <small class="text-muted" style="font-size: 0.7rem;"><?= date('M d', strtotime($msg['created_at'])) ?></small>
                            </div>
                            <p class="small text-muted mb-0 text-truncate"><?= htmlspecialchars(str_replace('ElectroTech', 'TechTower', $msg['message'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
