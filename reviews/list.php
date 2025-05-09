<?php
require_once '../config/db.php';
require_once '../utils/response.php';

$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    send_response(false, "Movie ID is required", null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.movie_id = ? ORDER BY r.created_at DESC");
    $stmt->execute([$movie_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Reviews fetched", $reviews);
} catch (PDOException $e) {
    send_response(false, "Fetch error: " . $e->getMessage(), null, 500);
}
