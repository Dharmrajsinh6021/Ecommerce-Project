<?php
/**
 * Database Configuration & Global Helpers
 * ElectroTech E-Commerce Project
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'techtower_db');

// Currency Configuration
define('CURRENCY', '₹');
define('SITE_NAME', 'TechTower');

// Establish PDO Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif;padding:30px;background:#fff3f3;border:1px solid #ffc1c1;max-width:650px;margin:50px auto;border-radius:8px;'>
        <h3 style='color:#dc3545;'>Database Connection Failed!</h3>
        <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p>Please make sure:</p>
        <ul>
            <li>Your MySQL service is running in XAMPP / WAMP.</li>
            <li>You have imported <code>database.sql</code> into phpMyAdmin.</li>
            <li>Database name <code>electrotech_db</code> exists.</li>
        </ul>
    </div>");
}

/**
 * Format price to Indian Rupee format
 */
function format_price($amount) {
    return CURRENCY . number_format((float)$amount, 2);
}

/**
 * Check if customer is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get total items count in shopping cart
 */
function get_cart_count() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += (int)($item['quantity'] ?? 0);
    }
    return $total;
}

/**
 * Sanitize user input
 */
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
