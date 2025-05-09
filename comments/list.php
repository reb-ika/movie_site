<?php
require_once '../config/db.php';
require_once '../utils/response.php';

$movie_id = $_GET['movie_id'] ?? null;
$review_id = $_GET['review_id'] ?? null;

if (!$movie_id && !$review_id) {
    send_response(false, "Movie ID or Review ID required", null, 400);
}

try {
    $query = "SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE ";
    $query .= $movie_id ? "c.movie_id = ?" : "c.review_id = ?";
    $id = $movie_id ?? $review_id;

    $stmt = $pdo->prepare($query . " AND c.parent_id IS NULL ORDER BY c.created_at DESC");
    $stmt->execute([$id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Comments fetched", $comments);
} catch (PDOException $e) {
    send_response(false, "List error: " . $e->getMessage(), null, 500);
}
