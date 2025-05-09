<?php
session_start();
require_once 'config/database.php';

// Check if database connection is initialized
if (!isset($pdo)) {
    die('Database connection not initialized.');
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Clear remember token from database if exists
if (isset($_COOKIE['remember_token'])) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (Exception $e) {
        error_log('Logout error clearing remember token: ' . $e->getMessage());
    }

    // Determine if connection is secure (for local dev HTTPS may not be enabled)
    $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    // Clear remember me cookie
    setcookie('remember_token', '', time() - 3600, '/', '', $isSecure, true);
}

// Unset all session variables
$_SESSION = [];

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Return JSON response if AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}

// Redirect to login page for non-AJAX requests
header('Location: login.php');
exit();

