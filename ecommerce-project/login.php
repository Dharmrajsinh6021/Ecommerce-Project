<?php
require_once __DIR__ . '/config/db.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: profile.php");
    exit;
}

$error = '';
$redirect = isset($_GET['redirect']) ? clean($_GET['redirect']) : 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = clean($_POST['redirect'] ?? 'index.php');

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR mobile = ?) AND role = 'customer'");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch();

        // Auto-seed demo customer if missing
        if (!$user && $email === 'customer@gmail.com' && $password === 'customer123') {
            $hashed = password_hash('customer123', PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (name, email, password, mobile, address, city, state, pincode, role, status) VALUES ('Rahul Sharma', 'customer@gmail.com', ?, '9898989898', 'Flat 402, Green Avenue', 'Ahmedabad', 'Gujarat', '380054', 'customer', 'active')");
            $ins->execute([$hashed]);
            $user = [
                'id' => $pdo->lastInsertId(),
                'name' => 'Rahul Sharma',
                'email' => 'customer@gmail.com',
                'password' => $hashed,
                'role' => 'customer',
                'status' => 'active'
            ];
        }

        if ($user) {
            if ($user['status'] === 'inactive') {
                $error = "Your account has been deactivated. Please contact support.";
            } elseif (password_verify($password, $user['password']) || $user['password'] === $password || ($user['email'] === 'customer@gmail.com' && $password === 'customer123')) {
                // Update to fresh secure hash if needed
                if (!password_verify($password, $user['password'])) {
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$new_hash, $user['id']]);
                }

                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                // Prevent open redirect
                if (empty($redirect) || str_contains($redirect, '://')) {
                    $redirect = 'index.php';
                }
                header("Location: " . $redirect);
                exit;
            } else {
                $error = "Invalid password. Please check your credentials.";
            }
        } else {
            $error = "No active customer account found with that email/mobile.";
        }
    }
}

$page_title = "Customer Login";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                        <i class="fa fa-user-lock fs-3"></i>
                    </div>
                    <h3 class="fw-bold">Welcome Back</h3>
                    <p class="text-muted small">Log in to view your orders, cart, and profile.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-4 small py-2">
                        <i class="fa fa-circle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email or Registered Mobile</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                            <input type="text" name="email" class="form-control" placeholder="customer@gmail.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label small fw-bold">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-key text-muted"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                            <i class="fa fa-sign-in me-1"></i> Sign In
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top small">
                    <span class="text-muted">Don't have an account?</span>
                    <a href="register.php?redirect=<?= urlencode($redirect) ?>" class="text-primary fw-bold text-decoration-none ms-1">Create Account</a>
                </div>

                <div class="alert alert-light border mt-3 mb-0 small text-muted text-center">
                    <strong>Demo Customer:</strong> customer@gmail.com<br>
                    <strong>Password:</strong> customer123
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
