<?php
require_once __DIR__ . '/../config/db.php';

// Fetch all active categories for navigation menu
try {
    $nav_cat_stmt = $pdo->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name ASC");
    $nav_categories = $nav_cat_stmt->fetchAll();
} catch (Exception $e) {
    $nav_categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?><?= SITE_NAME ?> - Premier Electronics Store</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons & Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #0b5ed7;
            --accent-color: #ffc107;
            --dark-color: #1e293b;
            --light-bg: #f8fafc;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.5px;
        }
        .navbar-brand span {
            color: var(--dark-color);
        }
        .badge-cart {
            font-size: 0.75rem;
            transform: translate(-25%, -25%);
        }
        .product-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }
        .product-img-wrapper {
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            overflow: hidden;
            padding: 15px;
        }
        .product-img-wrapper img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img-wrapper img {
            transform: scale(1.05);
        }
        .badge-discount {
            position: absolute;
            top: 12px;
            left: 12px;
            background-color: #ef4444;
            color: #fff;
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 6px;
        }
        .text-rating {
            color: #f59e0b;
        }
        .footer {
            background-color: var(--dark-color);
            color: #94a3b8;
        }
        .footer a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>

<!-- Top Notification Bar -->
<div class="bg-dark text-white py-1 px-3 d-none d-md-block border-bottom border-secondary small">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <span><i class="fa fa-truck text-warning me-2"></i> Free Express Delivery on orders over <?= CURRENCY ?>999!</span>
            <span class="ms-3"><i class="fa fa-phone text-warning me-2"></i> Support: +91 12345 67890</span>
        </div>
        <div>
            <a href="about.php" class="text-white text-decoration-none me-3">About Us</a>
            <a href="contact.php" class="text-white text-decoration-none me-3">Contact</a>
            <a href="admin/login.php" class="text-warning text-decoration-none fw-semibold"><i class="fa fa-lock me-1"></i> Admin Portal</a>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fa fa-bolt-lightning text-primary me-2 fs-3"></i>
            <span>Tech</span>Tower
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links & Search -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Search Form -->
            <form class="d-flex mx-auto my-2 my-lg-0 col-12 col-lg-5" action="products.php" method="GET">
                <div class="input-group">
                    <input class="form-control border-end-0 rounded-start-pill ps-3" type="search" name="search" placeholder="Search laptops, smartphones, earbuds..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" required>
                    <button class="btn btn-primary rounded-end-pill px-4" type="submit">
                        <i class="fa fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <!-- Nav Items -->
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-2" href="index.php">Home</a>
                </li>
                
                <!-- Categories Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold px-2" href="categories.php" id="catDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="catDropdown">
                        <li><a class="dropdown-item" href="categories.php"><strong>View All Categories</strong></a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($nav_categories as $cat): ?>
                            <li><a class="dropdown-item" href="products.php?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold px-2" href="products.php">All Products</a>
                </li>

                <!-- Cart Button -->
                <li class="nav-item me-lg-2">
                    <a class="btn btn-outline-primary position-relative rounded-pill px-3 py-1 my-1" href="cart.php">
                        <i class="fa fa-shopping-cart me-1"></i> Cart
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-cart">
                            <?= get_cart_count() ?>
                        </span>
                    </a>
                </li>

                <!-- User Account Dropdown -->
                <?php if (is_logged_in()): ?>
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle fw-semibold d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fa fa-circle-user fs-5 text-primary me-1"></i>
                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Account') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fa fa-user me-2 text-muted"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fa fa-box me-2 text-muted"></i> Order History</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa fa-arrow-right-from-bracket me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary rounded-pill px-3 py-1 my-1" href="login.php">
                            <i class="fa fa-user me-1"></i> Login / Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>
