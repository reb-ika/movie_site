<?php
require_once '../config/db.php';
require_once '../utils/response.php';

$movie_id = $_GET['id'] ?? null;

if (!$movie_id || !is_numeric($movie_id)) {
    send_response(false, "Invalid movie ID", null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$movie_id]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($movie) {
        send_response(true, "Movie details", $movie);
    } else {
        send_response(false, "Movie not found", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Error fetching movie: " . $e->getMessage(), null, 500);
}
