<?php
require_once __DIR__ . '/config/db.php';

// Auth and Cart validation
if (!is_logged_in() || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Check if this is the final order confirmation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $user_id         = (int)$_SESSION['user_id'];
    $customer_name   = clean($_POST['customer_name'] ?? '');
    $customer_email  = clean($_POST['customer_email'] ?? '');
    $customer_mobile = clean($_POST['customer_mobile'] ?? '');
    $shipping_address= clean($_POST['shipping_address'] ?? '');
    $city            = clean($_POST['city'] ?? '');
    $state           = clean($_POST['state'] ?? '');
    $pincode         = clean($_POST['pincode'] ?? '');
    $payment_method  = clean($_POST['payment_method'] ?? 'Cash on Delivery');
    $total_amount    = (float)($_POST['total_amount'] ?? 0);

    $payment_status = ($payment_method === 'Cash on Delivery') ? 'Pending' : 'Paid';
    $order_status   = 'Processing';

    try {
        $pdo->beginTransaction();

        // 1. Insert into orders table
        $order_stmt = $pdo->prepare("
            INSERT INTO orders 
            (user_id, customer_name, customer_email, customer_mobile, shipping_address, city, state, pincode, total_amount, payment_method, payment_status, order_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $order_stmt->execute([
            $user_id, $customer_name, $customer_email, $customer_mobile, 
            $shipping_address, $city, $state, $pincode, $total_amount, 
            $payment_method, $payment_status, $order_status
        ]);
        $order_id = $pdo->lastInsertId();

        // 2. Insert into order_items & deduct stock
        $item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stock_stmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");

        foreach ($_SESSION['cart'] as $p_id => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $item_stmt->execute([
                $order_id, $p_id, $item['name'], $item['price'], $item['quantity'], $subtotal
            ]);
            $stock_stmt->execute([$item['quantity'], $p_id]);
        }

        $pdo->commit();

        // Clear cart session
        $_SESSION['cart'] = [];

        // Redirect to success page
        header("Location: order-success.php?id=" . $order_id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Failed to place order: " . $e->getMessage();
    }
}

// Initial post from checkout.php
$customer_name    = clean($_POST['customer_name'] ?? '');
$customer_email   = clean($_POST['customer_email'] ?? '');
$customer_mobile  = clean($_POST['customer_mobile'] ?? '');
$shipping_address = clean($_POST['shipping_address'] ?? '');
$city             = clean($_POST['city'] ?? '');
$state            = clean($_POST['state'] ?? '');
$pincode          = clean($_POST['pincode'] ?? '');
$payment_method   = clean($_POST['payment_method'] ?? 'Cash on Delivery');
$total_amount     = (float)($_POST['total_amount'] ?? 0);

if (empty($customer_name) || empty($shipping_address)) {
    header("Location: checkout.php");
    exit;
}

$page_title = "Payment Authorization";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Confirm Payment</h1>
        <p class="mb-0 text-white-50">Review and authorize your transaction securely.</p>
    </div>
</div>

<div class="container mb-5">
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger rounded-4"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill mb-2">
                        <i class="fa fa-shield-halved me-1"></i> Secure 256-bit Payment Gateway
                    </span>
                    <h3 class="fw-bold">Payment Gateway</h3>
                    <p class="text-muted">Total Amount to be billed: <strong class="text-primary fs-4"><?= format_price($total_amount) ?></strong></p>
                </div>

                <form method="POST" action="payment.php">
                    <!-- Preserved Hidden Order Fields -->
                    <input type="hidden" name="confirm_order" value="1">
                    <input type="hidden" name="customer_name" value="<?= htmlspecialchars($customer_name) ?>">
                    <input type="hidden" name="customer_email" value="<?= htmlspecialchars($customer_email) ?>">
                    <input type="hidden" name="customer_mobile" value="<?= htmlspecialchars($customer_mobile) ?>">
                    <input type="hidden" name="shipping_address" value="<?= htmlspecialchars($shipping_address) ?>">
                    <input type="hidden" name="city" value="<?= htmlspecialchars($city) ?>">
                    <input type="hidden" name="state" value="<?= htmlspecialchars($state) ?>">
                    <input type="hidden" name="pincode" value="<?= htmlspecialchars($pincode) ?>">
                    <input type="hidden" name="payment_method" value="<?= htmlspecialchars($payment_method) ?>">
                    <input type="hidden" name="total_amount" value="<?= $total_amount ?>">

                    <!-- Payment Mode Interface -->
                    <?php if ($payment_method === 'Cash on Delivery'): ?>
                        <div class="alert alert-info border-info d-flex align-items-center p-3 rounded-4 mb-4">
                            <i class="fa fa-money-bill-wave fs-1 text-success me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Cash on Delivery Selected</h6>
                                <p class="mb-0 small text-secondary">You will pay <?= format_price($total_amount) ?> in cash or through QR scan when your order arrives at <strong><?= htmlspecialchars($shipping_address) ?>, <?= htmlspecialchars($city) ?></strong>.</p>
                            </div>
                        </div>

                    <?php elseif ($payment_method === 'UPI / QR Code'): ?>
                        <div class="text-center p-4 bg-light rounded-4 border mb-4">
                            <h6 class="fw-bold mb-2">Scan QR Code via Any UPI App</h6>
                            <p class="small text-muted mb-3">Google Pay, PhonePe, Paytm, or BHIM UPI</p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=upi://pay?pa=electrotech@okhdfcbank&pn=ElectroTech&am=<?= $total_amount ?>&cu=INR" alt="UPI QR" class="img-fluid rounded border p-2 bg-white shadow-sm mb-3">
                            <p class="small text-muted mb-0">UPI ID: <strong>electrotech@okhdfcbank</strong></p>
                        </div>

                    <?php else: ?>
                        <!-- Credit / Debit Card Simulated Form -->
                        <div class="bg-light p-4 rounded-4 border mb-4">
                            <h6 class="fw-bold mb-3"><i class="fa fa-credit-card text-primary me-2"></i>Enter Card Details (Demo Mode)</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Card Number</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="4111 2222 3333 4444" value="4532 •••• •••• 8892" required>
                                    <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" value="12/28" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">CVV</label>
                                    <input type="password" class="form-control" placeholder="•••" value="123" maxlength="3" required>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold py-3 shadow">
                            <i class="fa fa-check-circle me-2"></i> Authorize & Confirm Order (<?= format_price($total_amount) ?>)
                        </button>
                        <a href="checkout.php" class="btn btn-link text-muted small text-decoration-none text-center">
                            &larr; Return to edit shipping address
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
