<?php
$page_title = "Home";
require_once __DIR__ . '/includes/header.php';

// Fetch Categories for Featured Categories section
$cat_stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' LIMIT 4");
$categories = $cat_stmt->fetchAll();

// Fetch Featured Products (top 4 rated/priced)
$featured_stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'active' ORDER BY p.rating DESC, p.id DESC LIMIT 4");
$featured_products = $featured_stmt->fetchAll();

// Fetch New/Latest Products
$latest_stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'active' ORDER BY p.created_at DESC, p.id DESC LIMIT 8");
$latest_products = $latest_stmt->fetchAll();
?>

<!-- 1. Hero Banner / Slider -->
<div id="homeHeroCarousel" class="carousel slide carousel-fade mb-5 shadow-sm" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active" style="height: 440px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="container h-100 d-flex align-items-center">
                <div class="row align-items-center w-100">
                    <div class="col-lg-7 text-white ps-lg-4">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 text-uppercase">Next-Gen Flagship Series</span>
                        <h1 class="display-4 fw-extrabold mb-3">Titanium Strength.<br>Unstoppable Power.</h1>
                        <p class="lead mb-4 text-light">Explore revolutionary smartphones with breakthrough camera setups and lightning-fast silicon.</p>
                        <a href="products.php?category=1" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow">
                            <i class="fa fa-shopping-bag me-2"></i> Shop Flagships
                        </a>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <img src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&auto=format&fit=crop&q=80" alt="Smartphone" class="img-fluid rounded-4 shadow-lg" style="max-height: 360px; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item" style="height: 440px; background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
            <div class="container h-100 d-flex align-items-center">
                <div class="row align-items-center w-100">
                    <div class="col-lg-7 text-white ps-lg-4">
                        <span class="badge bg-danger px-3 py-2 rounded-pill fw-bold mb-3 text-uppercase">Supercharged Laptops</span>
                        <h1 class="display-4 fw-extrabold mb-3">Pro Performance.<br>All-Day Battery.</h1>
                        <p class="lead mb-4 text-light">From ultraportable notebooks to powerhouse RTX gaming laptops, upgrade your workflow today.</p>
                        <a href="products.php?category=2" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow">
                            <i class="fa fa-laptop me-2"></i> Explore Laptops
                        </a>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&auto=format&fit=crop&q=80" alt="Laptops" class="img-fluid rounded-4 shadow-lg" style="max-height: 360px; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item" style="height: 440px; background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%);">
            <div class="container h-100 d-flex align-items-center">
                <div class="row align-items-center w-100">
                    <div class="col-lg-7 text-white ps-lg-4">
                        <span class="badge bg-success px-3 py-2 rounded-pill fw-bold mb-3 text-uppercase">Pure Audio Bliss</span>
                        <h1 class="display-4 fw-extrabold mb-3">Active Noise Cancelling.<br>Studio Quality Sound.</h1>
                        <p class="lead mb-4 text-light">Immerse yourself in acoustic precision with top-rated wireless headphones and earbuds.</p>
                        <a href="products.php?category=3" class="btn btn-light btn-lg rounded-pill px-4 fw-bold shadow text-dark">
                            <i class="fa fa-headphones me-2"></i> Discover Audio
                        </a>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=80" alt="Audio" class="img-fluid rounded-4 shadow-lg" style="max-height: 360px; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<div class="container">

    <!-- Value Proposition Badges -->
    <div class="row g-3 mb-5 text-center">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border h-100">
                <i class="fa fa-truck-fast fs-2 text-primary mb-2"></i>
                <h6 class="fw-bold mb-1">Free Delivery</h6>
                <p class="small text-muted mb-0">On all orders over <?= CURRENCY ?>999</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border h-100">
                <i class="fa fa-shield-halved fs-2 text-success mb-2"></i>
                <h6 class="fw-bold mb-1">1 Year Warranty</h6>
                <p class="small text-muted mb-0">100% genuine guaranteed</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border h-100">
                <i class="fa fa-rotate-left fs-2 text-info mb-2"></i>
                <h6 class="fw-bold mb-1">7 Days Return</h6>
                <p class="small text-muted mb-0">Hassle-free replacement</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 shadow-sm border h-100">
                <i class="fa fa-headset fs-2 text-warning mb-2"></i>
                <h6 class="fw-bold mb-1">24/7 Support</h6>
                <p class="small text-muted mb-0">Dedicated tech experts</p>
            </div>
        </div>
    </div>

    <!-- 2. Featured Categories Section -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-primary fw-bold text-uppercase small">Browse Collections</span>
            <h2 class="fw-bold mb-0">Featured Categories</h2>
        </div>
        <a href="categories.php" class="btn btn-outline-primary rounded-pill btn-sm px-3">View All <i class="fa fa-arrow-right ms-1"></i></a>
    </div>

    <div class="row g-4 mb-5">
        <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-3">
                <a href="products.php?category=<?= $cat['id'] ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center rounded-4 overflow-hidden product-card">
                        <div style="height: 160px; overflow: hidden; background: #f1f5f9;">
                            <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
                            <span class="small text-primary fw-semibold">Explore Products &rarr;</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 3. Promotional Offer Banner -->
    <div class="card bg-primary text-white rounded-4 border-0 shadow-sm mb-5 p-4 p-md-5" style="background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%) !important;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2">MEGA FESTIVE SALE</span>
                <h2 class="display-6 fw-bold mb-2">Get Up to 35% OFF on Premium Audio & Wearables</h2>
                <p class="lead mb-0 text-white-50">Limited time promotion. Valid on top brands including Apple, Sony, and Samsung.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="products.php" class="btn btn-light btn-lg text-primary fw-bold rounded-pill px-4 shadow">Claim Offer Now</a>
            </div>
        </div>
    </div>

    <!-- 4. Featured Products Section -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-primary fw-bold text-uppercase small">Top Rated</span>
            <h2 class="fw-bold mb-0">Featured Products</h2>
        </div>
        <a href="products.php" class="btn btn-outline-primary rounded-pill btn-sm px-3">See Catalog <i class="fa fa-arrow-right ms-1"></i></a>
    </div>

    <div class="row g-4 mb-5">
        <?php foreach ($featured_products as $p): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 product-card position-relative">
                    <?php if (!empty($p['discount_price']) && $p['discount_price'] < $p['price']): ?>
                        <?php $discount_pct = round((($p['price'] - $p['discount_price']) / $p['price']) * 100); ?>
                        <span class="badge-discount">-<?= $discount_pct ?>% OFF</span>
                    <?php endif; ?>
                    
                    <a href="product-detail.php?id=<?= $p['id'] ?>" class="product-img-wrapper">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    </a>
                    
                    <div class="card-body d-flex flex-column p-3">
                        <span class="badge bg-light text-secondary border text-start w-auto align-self-start mb-2"><?= htmlspecialchars($p['category_name']) ?></span>
                        <h6 class="fw-bold text-dark mb-1">
                            <a href="product-detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark text-truncate d-block" title="<?= htmlspecialchars($p['name']) ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </a>
                        </h6>
                        <div class="mb-2 small text-rating">
                            <i class="fa fa-star"></i> <?= htmlspecialchars($p['rating']) ?> / 5.0
                        </div>
                        <div class="mt-auto mb-3">
                            <?php if (!empty($p['discount_price']) && $p['discount_price'] < $p['price']): ?>
                                <span class="fs-5 fw-bold text-primary"><?= format_price($p['discount_price']) ?></span>
                                <span class="text-muted text-decoration-line-through small ms-1"><?= format_price($p['price']) ?></span>
                            <?php else: ?>
                                <span class="fs-5 fw-bold text-primary"><?= format_price($p['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="product-detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-secondary btn-sm flex-fill rounded-pill">Details</a>
                            <form action="cart.php" method="POST" class="flex-fill">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                                    <i class="fa fa-cart-plus me-1"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 5. New / Latest Products Section -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-primary fw-bold text-uppercase small">Just Arrived</span>
            <h2 class="fw-bold mb-0">New & Latest Products</h2>
        </div>
        <a href="products.php" class="btn btn-outline-primary rounded-pill btn-sm px-3">View All Products <i class="fa fa-arrow-right ms-1"></i></a>
    </div>

    <div class="row g-4 mb-5">
        <?php foreach ($latest_products as $p): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 product-card position-relative">
                    <?php if (!empty($p['discount_price']) && $p['discount_price'] < $p['price']): ?>
                        <?php $discount_pct = round((($p['price'] - $p['discount_price']) / $p['price']) * 100); ?>
                        <span class="badge-discount">-<?= $discount_pct ?>% OFF</span>
                    <?php endif; ?>
                    
                    <a href="product-detail.php?id=<?= $p['id'] ?>" class="product-img-wrapper">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    </a>
                    
                    <div class="card-body d-flex flex-column p-3">
                        <span class="badge bg-light text-secondary border text-start w-auto align-self-start mb-2"><?= htmlspecialchars($p['category_name']) ?></span>
                        <h6 class="fw-bold text-dark mb-1">
                            <a href="product-detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark text-truncate d-block" title="<?= htmlspecialchars($p['name']) ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </a>
                        </h6>
                        <div class="mb-2 small text-rating">
                            <i class="fa fa-star"></i> <?= htmlspecialchars($p['rating']) ?> / 5.0
                        </div>
                        <div class="mt-auto mb-3">
                            <?php if (!empty($p['discount_price']) && $p['discount_price'] < $p['price']): ?>
                                <span class="fs-5 fw-bold text-primary"><?= format_price($p['discount_price']) ?></span>
                                <span class="text-muted text-decoration-line-through small ms-1"><?= format_price($p['price']) ?></span>
                            <?php else: ?>
                                <span class="fs-5 fw-bold text-primary"><?= format_price($p['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="product-detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-secondary btn-sm flex-fill rounded-pill">Details</a>
                            <form action="cart.php" method="POST" class="flex-fill">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                                    <i class="fa fa-cart-plus me-1"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
