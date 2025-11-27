<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * User (normal) login check
 * Called from files under /user or /public
 */
function checkLogin() {
    if (empty($_SESSION['user_id'])) {
        header("Location: ../public/login.php");
        exit;
    }
}

/**
 * Admin login check
 * Called from files under /admin
 */
function checkAdminLogin() {
    if (empty($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit;
    }
}
?>
