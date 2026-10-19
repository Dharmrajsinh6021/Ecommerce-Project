<?php
require_once __DIR__ . '/config/db.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

// Current Category Name
$category_name = "All Products";
if ($category_id > 0) {
    $c_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $c_stmt->execute([$category_id]);
    $cat_res = $c_stmt->fetch();
    if ($cat_res) {
        $category_name = $cat_res['name'];
    }
}

// Build SQL Query
$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'active'";
$params = [];

if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if (!empty($search_query)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY COALESCE(NULLIF(p.discount_price, 0), p.price) ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY COALESCE(NULLIF(p.discount_price, 0), p.price) DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY p.rating DESC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// All Categories for Sidebar Filter
$all_cats = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();

$page_title = $category_name;
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1"><?= htmlspecialchars($category_name) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="categories.php" class="text-white text-decoration-none">Categories</a></li>
                <li class="breadcrumb-item active text-white-50" aria-current="page"><?= htmlspecialchars($category_name) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
                <h6 class="fw-bold mb-3"><i class="fa fa-filter text-primary me-2"></i>Filter Categories</h6>
                <div class="list-group list-group-flush">
                    <a href="products.php<?= !empty($search_query) ? '?search=' . urlencode($search_query) : '' ?>" 
                       class="list-group-item list-group-item-action border-0 px-2 py-2 rounded <?= $category_id === 0 ? 'bg-primary text-white fw-bold' : '' ?>">
                       All Categories
                    </a>
                    <?php foreach ($all_cats as $cat): ?>
                        <a href="products.php?category=<?= $cat['id'] ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" 
                           class="list-group-item list-group-item-action border-0 px-2 py-2 rounded <?= $category_id === (int)$cat['id'] ? 'bg-primary text-white fw-bold' : '' ?>">
                           <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price Guarantee Card -->
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light text-center">
                <i class="fa fa-award text-warning fs-1 mb-2"></i>
                <h6 class="fw-bold mb-1">Genuine Quality</h6>
                <p class="small text-muted mb-0">Every product comes with brand authorized manufacturer warranty.</p>
            </div>
        </div>

        <!-- Product Listing Grid -->
        <div class="col-lg-9">
            <!-- Filter Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm mb-4 border">
                <div>
                    <span class="text-muted small">Showing <strong><?= count($products) ?></strong> products</span>
                    <?php if (!empty($search_query)): ?>
                        <span class="badge bg-secondary ms-2">Keyword: "<?= htmlspecialchars($search_query) ?>"</span>
                        <a href="products.php" class="text-danger small ms-1 text-decoration-none">&times; Clear</a>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    <label class="small text-muted mb-0 fw-semibold">Sort By:</label>
                    <form method="GET" class="d-inline">
                        <?php if ($category_id > 0): ?>
                            <input type="hidden" name="category" value="<?= $category_id ?>">
                        <?php endif; ?>
                        <?php if (!empty($search_query)): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">
                        <?php endif; ?>
                        <select name="sort" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                            <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Top Customer Rating</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Products -->
            <?php if (empty($products)): ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm p-4">
                    <i class="fa fa-box-open text-muted fs-1 mb-3"></i>
                    <h5 class="fw-bold">No Products Found</h5>
                    <p class="text-muted">We couldn't find any products matching your selection. Try browsing all categories.</p>
                    <a href="products.php" class="btn btn-primary rounded-pill px-4">Browse All Products</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $p): ?>
                        <div class="col-12 col-md-6 col-xl-4">
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
                                    
                                    <p class="text-muted small mb-2 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                                        <?= htmlspecialchars($p['description']) ?>
                                    </p>

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
                                        <a href="product-detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-secondary btn-sm flex-fill rounded-pill">View Details</a>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
