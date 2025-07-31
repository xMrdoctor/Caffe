<?php
require_once '../config/database.php';
initSecureSession();

// Log the logout event
if (isset($_SESSION['admin_username'])) {
    logSecurityEvent('LOGOUT', 'Admin logout: ' . $_SESSION['admin_username']);
}

// Destroy session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
header('Location: login.php');
exit;
?>