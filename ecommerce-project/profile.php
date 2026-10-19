<?php
require_once __DIR__ . '/config/db.php';

if (!is_logged_in()) {
    header("Location: login.php?redirect=profile.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$message = '';
$message_type = 'success';

// Handle Profile Update Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name    = clean($_POST['name'] ?? '');
        $email   = clean($_POST['email'] ?? '');
        $mobile  = clean($_POST['mobile'] ?? '');
        $address = clean($_POST['address'] ?? '');
        $city    = clean($_POST['city'] ?? '');
        $state   = clean($_POST['state'] ?? '');
        $pincode = clean($_POST['pincode'] ?? '');

        if (empty($name) || empty($email)) {
            $message = "Name and Email are required.";
            $message_type = 'danger';
        } else {
            // Check if email taken by another user
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check_stmt->execute([$email, $user_id]);
            if ($check_stmt->fetch()) {
                $message = "This email is already in use by another account.";
                $message_type = 'danger';
            } else {
                $up_stmt = $pdo->prepare("
                    UPDATE users 
                    SET name = ?, email = ?, mobile = ?, address = ?, city = ?, state = ?, pincode = ? 
                    WHERE id = ?
                ");
                $up_stmt->execute([$name, $email, $mobile, $address, $city, $state, $pincode, $user_id]);
                
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                $message = "Your profile has been updated successfully!";
                $message_type = 'success';
            }
        }
    }

    // Optional: Password change
    if (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass     = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        $p_stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $p_stmt->execute([$user_id]);
        $u_row = $p_stmt->fetch();

        if (!password_verify($current_pass, $u_row['password']) && $u_row['password'] !== $current_pass) {
            $message = "Current password does not match our records.";
            $message_type = 'danger';
        } elseif (strlen($new_pass) < 6) {
            $message = "New password must be at least 6 characters long.";
            $message_type = 'danger';
        } elseif ($new_pass !== $confirm_pass) {
            $message = "New password and confirmation do not match.";
            $message_type = 'danger';
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $up_p_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $up_p_stmt->execute([$new_hash, $user_id]);
            $message = "Password updated successfully!";
            $message_type = 'success';
        }
    }
}

// Fetch current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$page_title = "My Profile";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-primary text-white py-4 mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">My Customer Profile</h1>
        <p class="mb-0 text-white-50">Manage your contact details, default shipping address, and password.</p>
    </div>
</div>

<div class="container mb-5">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show rounded-4" role="alert">
            <i class="fa fa-info-circle me-2"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-white mb-4">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="fa fa-user fs-1"></i>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                <p class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></p>
                <span class="badge bg-success-subtle text-success border border-success px-3 py-1 rounded-pill">Active Account</span>
            </div>

            <div class="list-group shadow-sm rounded-4 border-0 overflow-hidden">
                <a href="profile.php" class="list-group-item list-group-item-action active py-3 fw-semibold">
                    <i class="fa fa-user-gear me-2"></i> Profile & Address
                </a>
                <a href="orders.php" class="list-group-item list-group-item-action py-3 fw-semibold">
                    <i class="fa fa-box-open me-2 text-primary"></i> Order History
                </a>
                <a href="logout.php" class="list-group-item list-group-item-action py-3 fw-semibold text-danger">
                    <i class="fa fa-arrow-right-from-bracket me-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Profile Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Edit Personal & Address Details</h5>

                <form method="POST" action="profile.php">
                    <input type="hidden" name="update_profile" value="1">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" placeholder="10-digit mobile">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">City</label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Street Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">State</label>
                            <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($user['state'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($user['pincode'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="fa fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Change Password</h5>

                <form method="POST" action="profile.php">
                    <input type="hidden" name="change_password" value="1">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Min 6 characters" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                            <i class="fa fa-key me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
