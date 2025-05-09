<?php
require_once '../config/db.php';
require_once '../utils/response.php';
require_once '../middleware/admin_check.php';

require_admin();

$data = json_decode(file_get_contents("php://input"), true);
$movie_id = $data['movie_id'] ?? null;

if (!$movie_id || !is_numeric($movie_id)) {
    send_response(false, "Invalid movie ID", null, 400);
}

try {
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$movie_id]);

    if ($stmt->rowCount() > 0) {
        send_response(true, "Movie deleted successfully");
    } else {
        send_response(false, "Movie not found", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Error deleting movie: " . $e->getMessage(), null, 500);
}
