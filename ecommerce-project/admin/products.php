<?php
$admin_page_title = "Product Inventory Management";
require_once __DIR__ . '/includes/header.php';

$message = '';
$message_type = 'success';

// Handle Add / Edit / Delete Product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. ADD PRODUCT
    if (isset($_POST['add_product'])) {
        $category_id    = (int)($_POST['category_id'] ?? 0);
        $name           = clean($_POST['name'] ?? '');
        $description    = clean($_POST['description'] ?? '');
        $price          = (float)($_POST['price'] ?? 0);
        $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
        $stock          = (int)($_POST['stock'] ?? 0);
        $rating         = (float)($_POST['rating'] ?? 4.5);
        $image          = clean($_POST['image'] ?? '');
        $specifications = clean($_POST['specifications'] ?? '');
        $status         = clean($_POST['status'] ?? 'active');

        if (empty($name) || $category_id <= 0 || $price <= 0) {
            $message = "Product Name, Category, and Valid Price are mandatory.";
            $message_type = 'danger';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO products (category_id, name, description, price, discount_price, stock, rating, image, specifications, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$category_id, $name, $description, $price, $discount_price, $stock, $rating, $image, $specifications, $status]);
            $message = "Product '$name' added to catalog successfully!";
        }
    }

    // 2. EDIT PRODUCT
    if (isset($_POST['edit_product'])) {
        $id             = (int)($_POST['id'] ?? 0);
        $category_id    = (int)($_POST['category_id'] ?? 0);
        $name           = clean($_POST['name'] ?? '');
        $description    = clean($_POST['description'] ?? '');
        $price          = (float)($_POST['price'] ?? 0);
        $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
        $stock          = (int)($_POST['stock'] ?? 0);
        $rating         = (float)($_POST['rating'] ?? 4.5);
        $image          = clean($_POST['image'] ?? '');
        $specifications = clean($_POST['specifications'] ?? '');
        $status         = clean($_POST['status'] ?? 'active');

        if ($id > 0 && !empty($name) && $price > 0) {
            $stmt = $pdo->prepare("
                UPDATE products 
                SET category_id = ?, name = ?, description = ?, price = ?, discount_price = ?, stock = ?, rating = ?, image = ?, specifications = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$category_id, $name, $description, $price, $discount_price, $stock, $rating, $image, $specifications, $status, $id]);
            $message = "Product #$id updated successfully!";
        } else {
            $message = "Missing required product details.";
            $message_type = 'danger';
        }
    }
}

// 3. DELETE PRODUCT VIA GET
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        $del_stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $del_stmt->execute([$del_id]);
        $message = "Product #$del_id deleted from catalog.";
        $message_type = 'warning';
    } catch (Exception $e) {
        $message = "Cannot delete product: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Fetch all categories for dropdown selection
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();

// Fetch products with category details
$products = $pdo->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
")->fetchAll();
?>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show rounded-4" role="alert">
        <i class="fa fa-info-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="fw-bold mb-0">All Products Inventory (<?= count($products) ?>)</h5>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fa fa-plus me-1"></i> Add Product
        </button>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="9" class="text-center py-4 text-muted">No products listed. Click 'Add Product' to start.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?= $p['id'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($p['image'] ?? 'https://via.placeholder.com/50') ?>" alt="" class="rounded-3 border" style="width: 45px; height: 45px; object-fit: contain; padding: 2px;">
                            </td>
                            <td class="fw-semibold text-dark" style="max-width: 200px;">
                                <div class="text-truncate" title="<?= htmlspecialchars($p['name']) ?>"><?= htmlspecialchars($p['name']) ?></div>
                                <small class="text-muted"><i class="fa fa-star text-warning"></i> <?= $p['rating'] ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($p['category_name']) ?></span>
                            </td>
                            <td class="fw-bold"><?= format_price($p['price']) ?></td>
                            <td>
                                <?= !empty($p['discount_price']) ? format_price($p['discount_price']) : '<span class="text-muted small">—</span>' ?>
                            </td>
                            <td>
                                <?php if ($p['stock'] > 5): ?>
                                    <span class="badge bg-success-subtle text-success border border-success"><?= $p['stock'] ?> in stock</span>
                                <?php elseif ($p['stock'] > 0): ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning">Low: <?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Out of stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $p['status'] === 'active' ? 'success' : 'danger' ?> rounded-pill">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-circle me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?= $p['id'] ?>" title="Edit Product">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <a href="products.php?delete=<?= $p['id'] ?>" class="btn btn-outline-danger btn-sm rounded-circle" 
                                   onclick="return confirm('Delete this product?')" title="Delete Product">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Product Modal -->
                        <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <form method="POST" action="products.php">
                                        <input type="hidden" name="edit_product" value="1">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">

                                        <div class="modal-header border-bottom">
                                            <h6 class="modal-title fw-bold">Edit Product #<?= $p['id'] ?></h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Product Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($p['name']) ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
                                                    <select name="category_id" class="form-select" required>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?= $cat['id'] ?>" <?= $p['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($cat['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Regular Price (<?= CURRENCY ?>) <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $p['price'] ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Discount Price (<?= CURRENCY ?>)</label>
                                                    <input type="number" step="0.01" name="discount_price" class="form-control" value="<?= $p['discount_price'] ?? '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Available Stock <span class="text-danger">*</span></label>
                                                    <input type="number" name="stock" class="form-control" value="<?= $p['stock'] ?>" required>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Product Image URL</label>
                                                    <input type="url" name="image" class="form-control" value="<?= htmlspecialchars($p['image']) ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Rating (0-5)</label>
                                                    <input type="number" step="0.1" name="rating" class="form-control" value="<?= $p['rating'] ?>" min="1" max="5">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" <?= $p['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= $p['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Description</label>
                                                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($p['description']) ?></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Specifications (Key: Value per line)</label>
                                                    <textarea name="specifications" class="form-control" rows="3"><?= htmlspecialchars($p['specifications']) ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Update Product</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form method="POST" action="products.php">
                <input type="hidden" name="add_product" value="1">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold"><i class="fa fa-boxes-packing text-primary me-2"></i>Add New Product to Catalog</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Product Title <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Wireless Noise Canceling Headphones" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Choose Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Regular Price (<?= CURRENCY ?>) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="19999.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Discount Price (<?= CURRENCY ?>)</label>
                            <input type="number" step="0.01" name="discount_price" class="form-control" placeholder="17999.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Initial Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" placeholder="25" value="10" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Product Image URL</label>
                            <input type="url" name="image" class="form-control" placeholder="https://images.unsplash.com/photo-...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Rating</label>
                            <input type="number" step="0.1" name="rating" class="form-control" value="4.8" min="1" max="5">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Overview and benefits of this device..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Specifications (Key: Value per line)</label>
                            <textarea name="specifications" class="form-control" rows="3" placeholder="Processor: 8-Core&#10;Battery: 5000 mAh&#10;Warranty: 1 Year"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Publish Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
