<?php
$admin_page_title = "Category Management";
require_once __DIR__ . '/includes/header.php';

$message = '';
$message_type = 'success';

// Handle Add / Edit / Delete Category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. ADD CATEGORY
    if (isset($_POST['add_category'])) {
        $name        = clean($_POST['name'] ?? '');
        $image       = clean($_POST['image'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $status      = clean($_POST['status'] ?? 'active');

        if (empty($name)) {
            $message = "Category Name is required.";
            $message_type = 'danger';
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, image, description, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $image, $description, $status]);
            $message = "Category added successfully!";
        }
    }

    // 2. EDIT CATEGORY
    if (isset($_POST['edit_category'])) {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = clean($_POST['name'] ?? '');
        $image       = clean($_POST['image'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $status      = clean($_POST['status'] ?? 'active');

        if ($id > 0 && !empty($name)) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, image = ?, description = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $image, $description, $status, $id]);
            $message = "Category #$id updated successfully!";
        } else {
            $message = "Category Name is required.";
            $message_type = 'danger';
        }
    }
}

// 3. DELETE CATEGORY VIA GET
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        $del_stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $del_stmt->execute([$del_id]);
        $message = "Category #$del_id deleted successfully!";
        $message_type = 'warning';
    } catch (Exception $e) {
        $message = "Cannot delete category: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Fetch all categories
$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.id DESC
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
        <h5 class="fw-bold mb-0">Product Categories (<?= count($categories) ?>)</h5>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa fa-plus me-1"></i> Add Category
        </button>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Image</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Products Count</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No categories created yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?= $cat['id'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($cat['image'] ?? 'https://via.placeholder.com/60') ?>" alt="" class="rounded-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="small text-muted" style="max-width: 250px;">
                                <div class="text-truncate"><?= htmlspecialchars($cat['description'] ?? 'N/A') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1">
                                    <?= $cat['product_count'] ?> Products
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $cat['status'] === 'active' ? 'success' : 'danger' ?> rounded-pill">
                                    <?= ucfirst($cat['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-circle me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?= $cat['id'] ?>" title="Edit Category">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <a href="categories.php?delete=<?= $cat['id'] ?>" class="btn btn-outline-danger btn-sm rounded-circle" 
                                   onclick="return confirm('Are you sure you want to delete this category and its associated products?')" title="Delete Category">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Category Modal for this row -->
                        <div class="modal fade" id="editModal<?= $cat['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4 border-0">
                                    <form method="POST" action="categories.php">
                                        <input type="hidden" name="edit_category" value="1">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">

                                        <div class="modal-header border-bottom">
                                            <h6 class="modal-title fw-bold">Edit Category #<?= $cat['id'] ?></h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Category Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Image URL</label>
                                                <input type="url" name="image" class="form-control" value="<?= htmlspecialchars($cat['image']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Description</label>
                                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($cat['description']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="active" <?= $cat['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= $cat['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Update Category</button>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form method="POST" action="categories.php">
                <input type="hidden" name="add_category" value="1">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold"><i class="fa fa-folder-plus text-primary me-2"></i>Create New Category</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Gaming Consoles" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Image URL</label>
                        <input type="url" name="image" class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this collection..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
