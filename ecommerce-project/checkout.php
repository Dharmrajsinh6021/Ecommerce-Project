<?php
require_once __DIR__ . '/config/db.php';

// Cart must not be empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// User must be logged in to checkout
if (!is_logged_in()) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

// Fetch current user information to pre-fill billing & shipping details
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();

// Compute totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
$shipping = ($subtotal > 999) ? 0.00 : 99.00;
$grand_total = $subtotal + $shipping;

$page_title = "Secure Checkout";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Checkout & Delivery Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="cart.php" class="text-white text-decoration-none">Cart</a></li>
                <li class="breadcrumb-item active text-white-50" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <form action="payment.php" method="POST">
        <div class="row g-4">
            <!-- Left Column: Customer & Address Information -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="fa fa-user text-primary me-2"></i>1. Customer Information
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_mobile" class="form-control" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" placeholder="10-digit mobile" required>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="fa fa-map-location-dot text-primary me-2"></i>2. Shipping & Billing Address
                    </h5>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Street Address / House No. / Flat <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" class="form-control" rows="2" placeholder="e.g. 402, Titanium City Center, 100 Feet Road" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="Ahmedabad" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">State <span class="text-danger">*</span></label>
                            <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($user['state'] ?? 'Gujarat') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Pincode <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($user['pincode'] ?? '') ?>" placeholder="380015" required>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="fa fa-credit-card text-primary me-2"></i>3. Select Payment Method
                    </h5>

                    <div class="list-group">
                        <label class="list-group-item d-flex gap-3 align-items-center py-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" value="Cash on Delivery" checked>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <h6 class="mb-0 fw-bold">Cash on Delivery (COD)</h6>
                                    <small class="text-muted">Pay in cash or via QR upon delivery at your doorstep.</small>
                                </div>
                                <i class="fa fa-money-bill-wave fs-3 text-success"></i>
                            </div>
                        </label>

                        <label class="list-group-item d-flex gap-3 align-items-center py-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" value="UPI / QR Code">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <h6 class="mb-0 fw-bold">Instant UPI (GPay / PhonePe / Paytm)</h6>
                                    <small class="text-muted">Fast and instant zero-fee digital payment.</small>
                                </div>
                                <i class="fa fa-qrcode fs-3 text-primary"></i>
                            </div>
                        </label>

                        <label class="list-group-item d-flex gap-3 align-items-center py-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" value="Credit/Debit Card">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <h6 class="mb-0 fw-bold">Credit / Debit Card</h6>
                                    <small class="text-muted">Visa, MasterCard, RuPay, Amex accepted.</small>
                                </div>
                                <i class="fa fa-credit-card fs-3 text-secondary"></i>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Order Summary (<?= count($_SESSION['cart']) ?> items)</h5>

                    <div class="list-group list-group-flush mb-3" style="max-height: 280px; overflow-y: auto;">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="rounded-2 me-2 border" style="width: 45px; height: 45px; object-fit: contain;">
                                    <div>
                                        <h6 class="small mb-0 text-truncate" style="max-width: 180px;"><?= htmlspecialchars($item['name']) ?></h6>
                                        <small class="text-muted">Qty: <?= $item['quantity'] ?> &times; <?= format_price($item['price']) ?></small>
                                    </div>
                                </div>
                                <span class="fw-semibold small"><?= format_price($item['price'] * $item['quantity']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold"><?= format_price($subtotal) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Delivery Charges:</span>
                        <?php if ($shipping == 0): ?>
                            <span class="text-success fw-bold">FREE</span>
                        <?php else: ?>
                            <span class="fw-semibold"><?= format_price($shipping) ?></span>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold mb-0">Total Payable:</span>
                        <span class="h4 fw-extrabold text-primary mb-0"><?= format_price($grand_total) ?></span>
                    </div>

                    <input type="hidden" name="total_amount" value="<?= $grand_total ?>">

                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow">
                        Proceed to Payment & Place Order <i class="fa fa-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center mt-3 small text-muted">
                        <i class="fa fa-shield-alt text-success me-1"></i> 100% Risk-Free Guarantee & Easy Returns
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
