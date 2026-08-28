<?php
/**
 * LeadDesk Mini - Authentication & Session Helper
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

/**
 * Checks if admin session is active
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Enforces admin authentication on protected pages
 */
function require_admin() {
    if (!is_admin_logged_in()) {
        $_SESSION['flash_error'] = "Authentication required. Please log in to access the admin portal.";
        header("Location: login.php");
        exit;
    }
}

/**
 * Generates or retrieves a unique CSRF token for the session
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a submitted CSRF token against the stored session token
 */
function verify_csrf_token($token) {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log in admin user session
 */
function login_admin($admin_id, $admin_email) {
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_email'] = $admin_email;
    $_SESSION['login_time'] = time();
}

/**
 * Logout admin user session
 */
function logout_admin() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
