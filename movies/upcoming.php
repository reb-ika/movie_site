<?php
require_once '../config/db.php';
require_once '../utils/response.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE release_date > CURDATE() ORDER BY release_date ASC");
    $stmt->execute();
    $upcoming = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Upcoming movies", $upcoming);
} catch (PDOException $e) {
    send_response(false, "Error fetching upcoming movies: " . $e->getMessage(), null, 500);
}
