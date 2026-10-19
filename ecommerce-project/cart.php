<?php
require_once __DIR__ . '/config/db.php';

// Initialize session cart array if not existing
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = '';
$message_type = 'info';

// Process Cart Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. ADD TO CART
    if ($action === 'add') {
        $p_id = (int)($_POST['product_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));

        if ($p_id > 0) {
            $stmt = $pdo->prepare("SELECT id, name, price, discount_price, stock, image FROM products WHERE id = ? AND status = 'active'");
            $stmt->execute([$p_id]);
            $prod = $stmt->fetch();

            if ($prod) {
                $final_price = (!empty($prod['discount_price']) && $prod['discount_price'] < $prod['price']) ? (float)$prod['discount_price'] : (float)$prod['price'];

                if (isset($_SESSION['cart'][$p_id])) {
                    $new_qty = $_SESSION['cart'][$p_id]['quantity'] + $qty;
                    $_SESSION['cart'][$p_id]['quantity'] = min($new_qty, $prod['stock']);
                } else {
                    $_SESSION['cart'][$p_id] = [
                        'id'       => $prod['id'],
                        'name'     => $prod['name'],
                        'price'    => $final_price,
                        'image'    => $prod['image'],
                        'stock'    => $prod['stock'],
                        'quantity' => min($qty, $prod['stock'])
                    ];
                }

                if (isset($_POST['buy_now']) && $_POST['buy_now'] == '1') {
                    header("Location: checkout.php");
                    exit;
                }

                $message = "Product added to your cart successfully!";
                $message_type = 'success';
            }
        }
    }

    // 2. UPDATE QUANTITY
    if ($action === 'update') {
        $p_id = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 1);

        if (isset($_SESSION['cart'][$p_id])) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$p_id]);
                $message = "Item removed from cart.";
            } else {
                $_SESSION['cart'][$p_id]['quantity'] = min($qty, $_SESSION['cart'][$p_id]['stock']);
                $message = "Cart updated.";
            }
            $message_type = 'success';
        }
    }
}

// 3. REMOVE ITEM VIA GET
if ($action === 'remove') {
    $p_id = (int)($_GET['id'] ?? 0);
    if (isset($_SESSION['cart'][$p_id])) {
        unset($_SESSION['cart'][$p_id]);
        $message = "Product removed from cart.";
        $message_type = 'warning';
    }
}

// 4. CLEAR ALL VIA GET
if ($action === 'clear') {
    $_SESSION['cart'] = [];
    $message = "Cart cleared.";
    $message_type = 'info';
}

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
$shipping = ($subtotal > 999 || $subtotal == 0) ? 0.00 : 99.00;
$grand_total = $subtotal + $shipping;

$page_title = "Shopping Cart";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Your Shopping Cart</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white-50" aria-current="page">Cart</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show rounded-4" role="alert">
            <i class="fa fa-info-circle me-2"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
            <div class="card-body">
                <i class="fa fa-cart-shopping text-muted display-1 mb-3"></i>
                <h3 class="fw-bold">Your Cart is Currently Empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any gadgets or devices to your cart yet.</p>
                <a href="products.php" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="fa fa-arrow-left me-2"></i> Start Shopping
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Items Table -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col" style="width: 150px;">Quantity</th>
                                    <th scope="col">Subtotal</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                                    <?php $item_subtotal = $item['price'] * $item['quantity']; ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded-3 me-3 border" style="width: 60px; height: 60px; object-fit: contain; padding: 4px;">
                                                <div>
                                                    <a href="product-detail.php?id=<?= $id ?>" class="fw-semibold text-dark text-decoration-none d-block">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <small class="text-muted">Stock: <?= $item['stock'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-semibold text-secondary">
                                            <?= format_price($item['price']) ?>
                                        </td>
                                        <td>
                                            <form action="cart.php" method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" class="form-control form-control-sm text-center me-2" style="width: 65px;" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="fw-bold text-primary">
                                            <?= format_price($item_subtotal) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="cart.php?action=remove&id=<?= $id ?>" class="btn btn-outline-danger btn-sm rounded-circle" title="Remove Item">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center border-top">
                        <a href="products.php" class="btn btn-outline-secondary rounded-pill btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Continue Shopping
                        </a>
                        <a href="cart.php?action=clear" class="btn btn-outline-danger rounded-pill btn-sm" onclick="return confirm('Clear all items from your cart?')">
                            <i class="fa fa-trash-can me-1"></i> Clear Cart
                        </a>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Order Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Items Subtotal:</span>
                        <span class="fw-semibold"><?= format_price($subtotal) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estimated Shipping:</span>
                        <?php if ($shipping == 0): ?>
                            <span class="text-success fw-bold">FREE</span>
                        <?php else: ?>
                            <span class="fw-semibold"><?= format_price($shipping) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Applicable Taxes:</span>
                        <span class="text-muted small">Included</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold mb-0">Grand Total:</span>
                        <span class="h4 fw-extrabold text-primary mb-0"><?= format_price($grand_total) ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm">
                        Proceed to Checkout <i class="fa fa-arrow-right ms-2"></i>
                    </a>

                    <div class="mt-3 text-center small text-muted">
                        <i class="fa fa-lock text-success me-1"></i> 256-bit Bank-Grade Encryption
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
