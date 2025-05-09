<?php
require_once '../config/db.php';
require_once '../utils/response.php';
require_once '../middleware/auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Only POST allowed', null, 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = get_authenticated_user_id();
$comment_id = $input['comment_id'] ?? null;

if (!$comment_id || !is_numeric($comment_id)) {
    send_response(false, 'Invalid comment ID', null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM comment_likes WHERE user_id = ? AND comment_id = ?");
    $stmt->execute([$user_id, $comment_id]);

    if ($stmt->rowCount() > 0) {
        $pdo->prepare("DELETE FROM comment_likes WHERE user_id = ? AND comment_id = ?")
            ->execute([$user_id, $comment_id]);
        send_response(true, 'Comment unliked');
    } else {
        $pdo->prepare("INSERT INTO comment_likes (user_id, comment_id) VALUES (?, ?)")
            ->execute([$user_id, $comment_id]);
        send_response(true, 'Comment liked');
    }

} catch (PDOException $e) {
    send_response(false, 'Database error: ' . $e->getMessage(), null, 500);
}
