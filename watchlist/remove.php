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
    $stmt = $pdo->prepare("DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$user_id, $movie_id]);

    if ($stmt->rowCount()) {
        send_response(true, "Movie removed from watchlist");
    } else {
        send_response(false, "Movie not found in watchlist", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
