<?php
require_once '../config/db.php';
require_once '../middleware/auth_check.php';
require_once '../utils/response.php';

require_auth();
$user_id = get_authenticated_user_id();

try {
    $stmt = $pdo->prepare("
        SELECT m.* FROM movies m
        INNER JOIN watchlist w ON m.id = w.movie_id
        WHERE w.user_id = ?
        ORDER BY w.id DESC
    ");
    $stmt->execute([$user_id]);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Watchlist retrieved", $movies);
} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
