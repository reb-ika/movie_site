<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

$data = json_decode(file_get_contents("php://input"), true);
$review_id = $data['review_id'] ?? null;
$new_rating = $data['rating'] ?? null;
$new_review = trim($data['review'] ?? '');

if (!$review_id || !$new_rating || $new_rating < 1 || $new_rating > 5) {
    send_response(false, "Invalid input", null, 400);
}

try {
    $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, review = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_rating, $new_review, $review_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        send_response(true, "Review updated");
    } else {
        send_response(false, "Review not found or not yours", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Update error: " . $e->getMessage(), null, 500);
}
