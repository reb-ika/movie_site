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
$review_id = $input['review_id'] ?? null;

if (!$review_id || !is_numeric($review_id)) {
    send_response(false, 'Invalid review ID', null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM review_likes WHERE user_id = ? AND review_id = ?");
    $stmt->execute([$user_id, $review_id]);

    if ($stmt->rowCount() > 0) {
        $pdo->prepare("DELETE FROM review_likes WHERE user_id = ? AND review_id = ?")
            ->execute([$user_id, $review_id]);
        send_response(true, 'Review unliked');
    } else {
        $pdo->prepare("INSERT INTO review_likes (user_id, review_id) VALUES (?, ?)")
            ->execute([$user_id, $review_id]);
        send_response(true, 'Review liked');
    }

} catch (PDOException $e) {
    send_response(false, 'Database error: ' . $e->getMessage(), null, 500);
}
