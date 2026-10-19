<?php
require_once __DIR__ . '/auth_check.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($admin_page_title) ? htmlspecialchars($admin_page_title) . ' | ' : '' ?>TechTower Admin Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #0f172a;
            --sidebar-color: #94a3b8;
            --sidebar-hover: #38bdf8;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
        }
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        #sidebar .nav-link {
            color: var(--sidebar-color);
            padding: 12px 20px;
            font-weight: 500;
            border-radius: 8px;
            margin: 4px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #ffffff;
            background-color: #1e293b;
            border-left: 4px solid var(--sidebar-hover);
        }
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #main-content {
                margin-left: 0;
            }
        }
        .stat-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<div id="sidebar" class="d-flex flex-column p-3">
    <div class="px-3 py-3 border-bottom border-secondary mb-3">
        <a href="index.php" class="text-white text-decoration-none d-flex align-items-center">
            <i class="fa fa-bolt-lightning text-warning fs-3 me-2"></i>
            <div>
                <h5 class="fw-bold mb-0 text-white">TechTower</h5>
                <small class="text-secondary text-uppercase fw-semibold" style="font-size: 0.65rem;">Admin Management</small>
            </div>
        </a>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="fa fa-gauge-high"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="categories.php" class="nav-link <?= $current_page === 'categories.php' ? 'active' : '' ?>">
                <i class="fa fa-layer-group"></i> Categories
            </a>
        </li>
        <li>
            <a href="products.php" class="nav-link <?= $current_page === 'products.php' ? 'active' : '' ?>">
                <i class="fa fa-boxes-stacked"></i> Products
            </a>
        </li>
        <li>
            <a href="orders.php" class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>">
                <i class="fa fa-cart-shopping"></i> Customer Orders
            </a>
        </li>
        <li>
            <a href="users.php" class="nav-link <?= $current_page === 'users.php' ? 'active' : '' ?>">
                <i class="fa fa-users"></i> Registered Users
            </a>
        </li>
    </ul>

    <hr class="border-secondary">

    <div class="px-2">
        <a href="../index.php" target="_blank" class="btn btn-outline-light btn-sm w-100 rounded-pill mb-2">
            <i class="fa fa-arrow-up-right-from-square me-1"></i> Visit Customer Store
        </a>
        <a href="logout.php" class="btn btn-danger btn-sm w-100 rounded-pill">
            <i class="fa fa-power-off me-1"></i> Logout
        </a>
    </div>
</div>

<!-- Main Content Area -->
<div id="main-content">
    <!-- Admin Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom py-3 px-4 sticky-top shadow-sm">
        <div class="container-fluid p-0">
            <button class="btn btn-light d-lg-none me-3" type="button" id="sidebarToggle">
                <i class="fa fa-bars"></i>
            </button>
            <h5 class="fw-bold mb-0 text-dark"><?= isset($admin_page_title) ? htmlspecialchars($admin_page_title) : 'Dashboard' ?></h5>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="d-flex align-items-center text-dark">
                    <span class="rounded-circle bg-primary-subtle text-primary p-2 me-2">
                        <i class="fa fa-user-shield"></i>
                    </span>
                    <div class="d-none d-sm-block text-end">
                        <div class="fw-bold small"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                        <small class="text-muted" style="font-size: 0.75rem;">Super Administrator</small>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="p-4 flex-grow-1">
