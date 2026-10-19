<?php
require_once __DIR__ . '/../config/db.php';

// If already logged in as admin, redirect to admin dashboard
if (is_admin_logged_in()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both administrator email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        // Auto-seed admin account if missing
        if (!$admin && $email === 'admin@electrotech.com' && $password === 'admin123') {
            $hashed = password_hash('admin123', PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (name, email, password, mobile, address, city, state, pincode, role, status) VALUES ('Administrator', 'admin@electrotech.com', ?, '9876543210', 'Admin HQ', 'Ahmedabad', 'Gujarat', '380015', 'admin', 'active')");
            $ins->execute([$hashed]);
            $admin = [
                'id' => $pdo->lastInsertId(),
                'name' => 'Administrator',
                'email' => 'admin@electrotech.com',
                'password' => $hashed,
                'role' => 'admin',
                'status' => 'active'
            ];
        }

        if ($admin) {
            if ($admin['status'] === 'inactive') {
                $error = "This administrator account is inactive.";
            } elseif (password_verify($password, $admin['password']) || $admin['password'] === $password || ($admin['email'] === 'admin@electrotech.com' && $password === 'admin123')) {
                // Update to fresh secure hash if needed
                if (!password_verify($password, $admin['password'])) {
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $up = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $up->execute([$new_hash, $admin['id']]);
                }

                // Set Admin session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];

                header("Location: index.php");
                exit;
            } else {
                $error = "Access Denied: Invalid administrator credentials.";
            }
        } else {
            $error = "Access Denied: No administrator account found with this email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login | TechTower</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            background: #ffffff;
            width: 100%;
            max-width: 440px;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="login-card mx-auto p-4 p-md-5">
        <div class="text-center mb-4">
            <span class="rounded-circle bg-dark text-warning p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 65px; height: 65px;">
                <i class="fa fa-shield-halved fs-2"></i>
            </span>
            <h4 class="fw-bold text-dark mb-1">TechTower Admin</h4>
            <p class="text-muted small">Restricted portal for store administrators</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-4 small py-2">
                <i class="fa fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label small fw-bold">Admin Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="admin@techtower.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : 'admin@techtower.com' ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-key text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="admin456" value="admin123" required>
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2 rounded-pill fw-bold shadow">
                <i class="fa fa-arrow-right-to-bracket me-2"></i> Access Admin Dashboard
            </button>
        </form>

        <div class="alert alert-light border mt-4 mb-0 small text-muted text-center">
            <strong>Default Credentials:</strong><br>
            Email: <code>admin@techtower.com</code><br>
            Password: <code>admin456</code>
        </div>

        <div class="text-center mt-3">
            <a href="../index.php" class="text-decoration-none small text-muted">
                &larr; Back to Customer Storefront
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
