<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/response.php';

function require_admin() {
    require_auth(); // Ensure user is logged in

    $user_id = $_SESSION['user_id'];

    try {
        global $pdo;
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || strtolower($user['role']) !== 'admin') {
            send_response(false, 'Admin privileges required', null, 403);
            exit;
        }
    } catch (PDOException $e) {
        send_response(false, 'Database error: ' . $e->getMessage(), null, 500);
        exit;
    }
}
