<?php
require_once __DIR__ . '/config/db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: products.php");
    exit;
}

// Fetch product details
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.status = 'active'
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

// Fetch related products from same category
$rel_stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
    LIMIT 4
");
$rel_stmt->execute([$product['category_id'], $product_id]);
$related_products = $rel_stmt->fetchAll();

$page_title = $product['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="categories.php" class="text-decoration-none">Categories</a></li>
                <li class="breadcrumb-item"><a href="products.php?category=<?= $product['category_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 300px;"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">
        <!-- Product Image Showcase -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white position-relative">
                <?php if (!empty($product['discount_price']) && $product['discount_price'] < $product['price']): ?>
                    <?php $discount_pct = round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>
                    <span class="badge bg-danger position-absolute top-0 start-0 m-3 fs-6 px-3 py-2 rounded-pill">
                        Save <?= $discount_pct ?>%
                    </span>
                <?php endif; ?>
                <div style="min-height: 380px; display: flex; align-items: center; justify-content: center;">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded-3" style="max-height: 380px; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="ps-lg-3">
                <a href="products.php?category=<?= $product['category_id'] ?>" class="badge bg-light text-primary border text-decoration-none mb-2 px-3 py-2">
                    <?= htmlspecialchars($product['category_name']) ?>
                </a>

                <h1 class="h2 fw-bold text-dark mb-2"><?= htmlspecialchars($product['name']) ?></h1>

                <!-- Ratings and Reviews -->
                <div class="d-flex align-items-center mb-3">
                    <div class="text-rating me-2">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star-half-stroke"></i>
                    </div>
                    <span class="fw-bold me-2"><?= htmlspecialchars($product['rating']) ?></span>
                    <span class="text-muted small">| Brand Certified Authentic</span>
                </div>

                <!-- Price Box -->
                <div class="p-3 bg-light rounded-4 mb-4 border">
                    <div class="d-flex align-items-baseline gap-3">
                        <?php if (!empty($product['discount_price']) && $product['discount_price'] < $product['price']): ?>
                            <span class="display-6 fw-extrabold text-primary"><?= format_price($product['discount_price']) ?></span>
                            <span class="text-muted text-decoration-line-through fs-5"><?= format_price($product['price']) ?></span>
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1 small">
                                Save <?= format_price($product['price'] - $product['discount_price']) ?>
                            </span>
                        <?php else: ?>
                            <span class="display-6 fw-extrabold text-primary"><?= format_price($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="small text-muted mt-1">Inclusive of all taxes & warranty. Free Express Shipping available.</div>
                </div>

                <!-- Stock Availability -->
                <div class="mb-4">
                    <span class="fw-semibold">Availability:</span>
                    <?php if ($product['stock'] > 0): ?>
                        <span class="badge bg-success-subtle text-success border border-success ms-2 px-3 py-1">
                            <i class="fa fa-check-circle me-1"></i> In Stock (<?= $product['stock'] ?> units left)
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger ms-2 px-3 py-1">
                            <i class="fa fa-times-circle me-1"></i> Currently Out of Stock
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark">Product Overview:</h6>
                    <p class="text-secondary leading-relaxed">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </p>
                </div>

                <!-- Purchase Actions -->
                <?php if ($product['stock'] > 0): ?>
                    <form action="cart.php" method="POST" class="mb-4">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <label for="quantityInput" class="col-form-label fw-bold">Quantity:</label>
                            </div>
                            <div class="col-auto" style="width: 120px;">
                                <input type="number" id="quantityInput" name="quantity" class="form-control text-center" value="1" min="1" max="<?= $product['stock'] ?>" required>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 flex-fill shadow-sm">
                                <i class="fa fa-cart-shopping me-2"></i> Add to Cart
                            </button>
                            <button type="submit" name="buy_now" value="1" class="btn btn-warning btn-lg rounded-pill px-4 flex-fill fw-bold shadow-sm">
                                <i class="fa fa-bolt me-2"></i> Buy Now
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg rounded-pill px-4 w-100 disabled mb-4" disabled>
                        Item Out of Stock
                    </button>
                <?php endif; ?>

                <!-- Feature checklist -->
                <div class="row g-2 border-top pt-3 small text-muted">
                    <div class="col-6"><i class="fa fa-rotate-left text-primary me-2"></i> 7 Days Easy Replacement</div>
                    <div class="col-6"><i class="fa fa-shield-check text-primary me-2"></i> 1 Year Manufacturer Warranty</div>
                    <div class="col-6"><i class="fa fa-truck-fast text-primary me-2"></i> Lightning Fast Delivery</div>
                    <div class="col-6"><i class="fa fa-lock text-primary me-2"></i> 100% Secure Checkout</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Specifications Table -->
    <?php if (!empty($product['specifications'])): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h4 class="fw-bold mb-3 border-bottom pb-2">Technical Specifications</h4>
                    <table class="table table-striped table-hover mb-0">
                        <tbody>
                            <?php 
                            $specs = explode("\n", $product['specifications']);
                            foreach ($specs as $spec):
                                if (trim($spec) === '') continue;
                                $parts = explode(':', $spec, 2);
                            ?>
                                <tr>
                                    <th style="width: 30%;" class="text-secondary"><?= htmlspecialchars(trim($parts[0] ?? '')) ?></th>
                                    <td><?= htmlspecialchars(trim($parts[1] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="mt-5">
            <h4 class="fw-bold mb-4">Related Products You Might Like</h4>
            <div class="row g-4">
                <?php foreach ($related_products as $rp): ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <a href="product-detail.php?id=<?= $rp['id'] ?>" class="product-img-wrapper">
                                <img src="<?= htmlspecialchars($rp['image']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>">
                            </a>
                            <div class="card-body d-flex flex-column p-3">
                                <h6 class="fw-bold text-dark mb-1">
                                    <a href="product-detail.php?id=<?= $rp['id'] ?>" class="text-decoration-none text-dark text-truncate d-block">
                                        <?= htmlspecialchars($rp['name']) ?>
                                    </a>
                                </h6>
                                <div class="mt-auto">
                                    <span class="fs-6 fw-bold text-primary">
                                        <?= format_price(!empty($rp['discount_price']) ? $rp['discount_price'] : $rp['price']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
