<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

$data = json_decode(file_get_contents("php://input"), true);
$movie_id = $data['movie_id'] ?? null;
$rating = $data['rating'] ?? null;
$review = trim($data['review'] ?? '');

if (!$movie_id || !$rating || $rating < 1 || $rating > 5) {
    send_response(false, "Invalid rating or movie ID", null, 400);
}

try {
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, movie_id, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $movie_id, $rating, $review]);

    send_response(true, "Review added");
} catch (PDOException $e) {
    send_response(false, "Error: " . $e->getMessage(), null, 500);
}
