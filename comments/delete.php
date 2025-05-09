<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

$data = json_decode(file_get_contents("php://input"), true);
$comment_id = $data['comment_id'] ?? null;

if (!$comment_id) {
    send_response(false, "Comment ID required", null, 400);
}

try {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        send_response(true, "Comment deleted");
    } else {
        send_response(false, "Comment not found or unauthorized", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Delete error: " . $e->getMessage(), null, 500);
}
