<?php
require_once '../config/db.php';
require_once '../middleware/admin_check.php';
require_once '../utils/response.php';

check_admin(); // Ensure only admins can access

// Get statistics per genre
try {
    $stmt = $pdo->query("SELECT g.name AS genre, COUNT(m.id) AS movie_count 
                         FROM genres g
                         LEFT JOIN movies m ON m.genre_id = g.id
                         GROUP BY g.id");
    $genre_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Statistics retrieved", $genre_stats);

} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
