<?php
$admin_page_title = "Customer Accounts Management";
require_once __DIR__ . '/includes/header.php';

$message = '';
$message_type = 'success';

// Handle Toggle Status / Delete User
if (isset($_GET['toggle_status'])) {
    $uid = (int)$_GET['toggle_status'];
    $current = clean($_GET['current'] ?? 'active');
    $new_status = ($current === 'active') ? 'inactive' : 'active';

    $up_stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'customer'");
    $up_stmt->execute([$new_status, $uid]);
    $message = "User #$uid account status toggled to $new_status.";
}

if (isset($_GET['delete'])) {
    $del_uid = (int)$_GET['delete'];
    try {
        $del_stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
        $del_stmt->execute([$del_uid]);
        $message = "Customer #$del_uid deleted successfully.";
        $message_type = 'warning';
    } catch (Exception $e) {
        $message = "Cannot delete user: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Search functionality
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'customer' AND (name LIKE ? OR email LIKE ? OR mobile LIKE ? OR city LIKE ?) ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'customer' ORDER BY id DESC");
}
$users = $stmt->fetchAll();
?>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show rounded-4" role="alert">
        <i class="fa fa-info-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
        <h5 class="fw-bold mb-0">Registered Customers (<?= count($users) ?>)</h5>
        
        <!-- Search Bar -->
        <form method="GET" action="users.php" class="d-flex" style="max-width: 320px;">
            <input type="text" name="search" class="form-control form-control-sm rounded-start-pill ps-3" placeholder="Search by name, email, mobile..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary btn-sm rounded-end-pill px-3" type="submit">
                <i class="fa fa-search"></i>
            </button>
            <?php if (!empty($search)): ?>
                <a href="users.php" class="btn btn-outline-secondary btn-sm ms-2 rounded-pill">&times;</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-4">User ID</th>
                    <th>Customer Name</th>
                    <th>Email Address</th>
                    <th>Mobile</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Registered On</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">No customer accounts found matching criteria.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?= $u['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($u['address'] ?? 'No address') ?></small>
                            </td>
                            <td class="small"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="small"><?= htmlspecialchars($u['mobile'] ?? 'N/A') ?></td>
                            <td class="small"><?= htmlspecialchars($u['city'] ?? 'N/A') ?>, <?= htmlspecialchars($u['state'] ?? '') ?></td>
                            <td>
                                <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'danger' ?> rounded-pill">
                                    <?= ucfirst($u['status']) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td class="text-center">
                                <a href="users.php?toggle_status=<?= $u['id'] ?>&current=<?= $u['status'] ?>" 
                                   class="btn btn-sm btn-outline-<?= $u['status'] === 'active' ? 'warning' : 'success' ?> rounded-pill px-2 py-1 small" 
                                   title="Toggle Account Status">
                                    <?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </a>
                                <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-outline-danger btn-sm rounded-circle ms-1" 
                                   onclick="return confirm('Delete this user account?')" title="Delete Customer">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
