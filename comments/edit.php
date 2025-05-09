<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

$data = json_decode(file_get_contents("php://input"), true);
$comment_id = $data['comment_id'] ?? null;
$content = trim($data['content'] ?? '');

if (!$comment_id || !$content) {
    send_response(false, "Missing fields", null, 400);
}

try {
    $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$content, $comment_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        send_response(true, "Comment updated");
    } else {
        send_response(false, "Comment not found or unauthorized", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Edit error: " . $e->getMessage(), null, 500);
}
