<?php
require_once '../config/db.php';
require_once '../middleware/admin_check.php';
require_once '../utils/response.php';

check_admin(); // Ensure only admins can access

// Get all users
try {
    $stmt = $pdo->query("SELECT id, username, email, status, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Users retrieved", $users);

} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
