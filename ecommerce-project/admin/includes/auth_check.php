<?php
require_once __DIR__ . '/../../config/db.php';

// Verify admin session exists
if (!is_admin_logged_in()) {
    header("Location: login.php");
    exit;
}
?>
