<?php
require_once '../config/db.php';
require_once '../utils/response.php';

$parent_id = $_GET['parent_id'] ?? null;

if (!$parent_id || !is_numeric($parent_id)) {
    send_response(false, "Parent comment ID required", null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.parent_id = ? ORDER BY c.created_at ASC");
    $stmt->execute([$parent_id]);
    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Replies fetched", $replies);
} catch (PDOException $e) {
    send_response(false, "Fetch error: " . $e->getMessage(), null, 500);
}
