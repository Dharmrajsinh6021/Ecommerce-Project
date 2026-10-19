<?php
require_once __DIR__ . '/../config/db.php';

unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);

header("Location: login.php");
exit;
?>
