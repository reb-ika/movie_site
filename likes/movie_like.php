<?php
require_once '../config/db.php';
require_once '../utils/response.php';
require_once '../middleware/auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Only POST allowed', null, 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = get_authenticated_user_id(); // From token/session
$movie_id = $input['movie_id'] ?? null;

if (!$movie_id || !is_numeric($movie_id)) {
    send_response(false, 'Invalid movie ID', null, 400);
}

try {
    // Check if user already liked the movie
    $stmt = $pdo->prepare("SELECT * FROM movie_likes WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$user_id, $movie_id]);

    if ($stmt->rowCount() > 0) {
        // Unlike
        $pdo->prepare("DELETE FROM movie_likes WHERE user_id = ? AND movie_id = ?")
            ->execute([$user_id, $movie_id]);
        send_response(true, 'Movie unliked');
    } else {
        // Like
        $pdo->prepare("INSERT INTO movie_likes (user_id, movie_id) VALUES (?, ?)")
            ->execute([$user_id, $movie_id]);
        send_response(true, 'Movie liked');
    }

} catch (PDOException $e) {
    send_response(false, 'Database error: ' . $e->getMessage(), null, 500);
}
