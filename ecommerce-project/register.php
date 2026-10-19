<?php
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header("Location: profile.php");
    exit;
}

$error = '';
$success = '';
$redirect = isset($_GET['redirect']) ? clean($_GET['redirect']) : 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = clean($_POST['name'] ?? '');
    $email            = clean($_POST['email'] ?? '');
    $mobile           = clean($_POST['mobile'] ?? '');
    $address          = clean($_POST['address'] ?? '');
    $city             = clean($_POST['city'] ?? '');
    $state            = clean($_POST['state'] ?? 'Gujarat');
    $pincode          = clean($_POST['pincode'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $redirect         = clean($_POST['redirect'] ?? 'index.php');

    // Validation checks
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all mandatory fields (Name, Email, Password).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Password and confirm password do not match.";
    } else {
        // Check uniqueness of email
        $chk_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $chk_stmt->execute([$email]);
        if ($chk_stmt->fetch()) {
            $error = "An account with this email address already exists. Please log in.";
        } else {
            // Password hashing as required by assignment specification
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $ins_stmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, mobile, address, city, state, pincode, role, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'customer', 'active')
                ");
                $ins_stmt->execute([$name, $email, $hashed_password, $mobile, $address, $city, $state, $pincode]);
                $new_user_id = $pdo->lastInsertId();

                // Auto login the newly registered user
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                if (empty($redirect) || str_contains($redirect, '://')) {
                    $redirect = 'index.php';
                }
                header("Location: " . $redirect);
                exit;
            } catch (Exception $e) {
                $error = "Registration error: " . $e->getMessage();
            }
        }
    }
}

$page_title = "Register New Account";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                        <i class="fa fa-user-plus fs-3"></i>
                    </div>
                    <h3 class="fw-bold">Create Customer Account</h3>
                    <p class="text-muted small">Join ElectroTech to track your orders, save delivery addresses, and enjoy faster checkout.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-4 small py-2">
                        <i class="fa fa-circle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="john@example.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile" value="<?= isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '' ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">City</label>
                            <input type="text" name="city" class="form-control" placeholder="Ahmedabad" value="<?= isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '' ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Delivery Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="House/Flat number, Street, Landmark"><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">State</label>
                            <input type="text" name="state" class="form-control" value="<?= isset($_POST['state']) ? htmlspecialchars($_POST['state']) : 'Gujarat' ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pincode</label>
                            <input type="text" name="pincode" class="form-control" placeholder="380015" value="<?= isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : '' ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Re-type password" required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                            <i class="fa fa-user-check me-1"></i> Register Account
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top small">
                    <span class="text-muted">Already registered?</span>
                    <a href="login.php?redirect=<?= urlencode($redirect) ?>" class="text-primary fw-bold text-decoration-none ms-1">Log in here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
