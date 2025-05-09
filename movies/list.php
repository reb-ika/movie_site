<?php
require_once '../config/db.php';
require_once '../utils/response.php';

try {
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC");
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Movies fetched", $movies);
} catch (PDOException $e) {
    send_response(false, "Failed to fetch movies: " . $e->getMessage(), null, 500);
}
