<?php
require_once '../config/db.php';
require_once '../utils/response.php';
require_once '../middleware/admin_check.php';

require_admin();

$data = json_decode(file_get_contents("php://input"), true);

$movie_id = $data['id'] ?? null;
$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$genre = trim($data['genre'] ?? '');
$release_date = $data['release_date'] ?? '';
$poster_path = $data['poster_path'] ?? null;

if (!$movie_id || !is_numeric($movie_id) || !$title || !$genre || !$release_date) {
    send_response(false, "Missing or invalid fields", null, 400);
}

try {
    $stmt = $pdo->prepare("UPDATE movies SET title = ?, description = ?, genre = ?, release_date = ?, poster_path = ? WHERE id = ?");
    $stmt->execute([$title, $description, $genre, $release_date, $poster_path, $movie_id]);

    if ($stmt->rowCount() > 0) {
        send_response(true, "Movie updated successfully");
    } else {
        send_response(false, "No changes made or movie not found", null, 404);
    }
} catch (PDOException $e) {
    send_response(false, "Error updating movie: " . $e->getMessage(), null, 500);
}
