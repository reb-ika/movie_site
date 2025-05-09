<?php
require_once '../config/db.php';
require_once '../utils/response.php';

$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    send_response(false, "Movie ID is required", null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT AVG(rating) as average_rating FROM reviews WHERE movie_id = ?");
    $stmt->execute([$movie_id]);
    $average = $stmt->fetch(PDO::FETCH_ASSOC);

    send_response(true, "Average rating", $average);
} catch (PDOException $e) {
    send_response(false, "Rating error: " . $e->getMessage(), null, 500);
}
