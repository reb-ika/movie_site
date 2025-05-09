<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

$data = json_decode(file_get_contents("php://input"), true);
$review_id = $data['review_id'] ?? null;

if (!$review_id) {
    send_response(false, "Missing review ID", null, 400);
}

try {
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $user_id]);

    if ($stmt->rowCount()) {
        send_response(true, "Review deleted");
    } else {
        send_response(false, "Review not found or unauthorized", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Delete error: " . $e->getMessage(), null, 500);
}
