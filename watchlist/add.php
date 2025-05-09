<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

$data = json_decode(file_get_contents("php://input"), true);
$movie_id = $data['movie_id'] ?? null;

if (!$movie_id || !is_numeric($movie_id)) {
    send_response(false, "Invalid movie ID", null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$user_id, $movie_id]);
    if ($stmt->fetch()) {
        send_response(false, "Movie already in watchlist", null, 409);
    }

    $stmt = $pdo->prepare("INSERT INTO watchlist (user_id, movie_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $movie_id]);

    send_response(true, "Movie added to watchlist");
} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
