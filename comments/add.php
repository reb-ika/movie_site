<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

$data = json_decode(file_get_contents("php://input"), true);
$content = trim($data['content'] ?? '');
$movie_id = $data['movie_id'] ?? null;
$review_id = $data['review_id'] ?? null;
$parent_id = $data['parent_id'] ?? null;

if (!$content || (!$movie_id && !$review_id)) {
    send_response(false, "Missing required data", null, 400);
}

try {
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, movie_id, review_id, parent_id, content) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $movie_id, $review_id, $parent_id, $content]);

    send_response(true, "Comment added");
} catch (PDOException $e) {
    send_response(false, "Add error: " . $e->getMessage(), null, 500);
}
