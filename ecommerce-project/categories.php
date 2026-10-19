<?php
$page_title = "Product Categories";
require_once __DIR__ . '/includes/header.php';

// Fetch categories along with product counts
$cat_stmt = $pdo->query("
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
    WHERE c.status = 'active'
    GROUP BY c.id
    ORDER BY c.name ASC
");
$categories = $cat_stmt->fetchAll();
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Browse Categories</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white-50" aria-current="page">Categories</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <?php if (empty($categories)): ?>
            <div class="col-12 text-center py-5">
                <div class="alert alert-info">No categories found in the store.</div>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 product-card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div style="height: 200px; overflow: hidden; background: #e2e8f0;">
                            <img src="<?= htmlspecialchars($cat['image'] ?? 'https://via.placeholder.com/400x300?text=No+Image') ?>" 
                                 alt="<?= htmlspecialchars($cat['name']) ?>" 
                                 class="w-100 h-100" 
                                 style="object-fit: cover; transition: transform 0.3s ease;">
                        </div>
                        <div class="card-body p-4 d-flex flex-column text-center">
                            <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($cat['name']) ?></h5>
                            <p class="text-muted small mb-3 flex-grow-1">
                                <?= htmlspecialchars($cat['description'] ?? 'Discover premium items in this category.') ?>
                            </p>
                            <span class="badge bg-light text-primary border mb-3 py-2">
                                <?= $cat['product_count'] ?> Products Available
                            </span>
                            <a href="products.php?category=<?= $cat['id'] ?>" class="btn btn-outline-primary rounded-pill w-100 fw-semibold">
                                View Products <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
