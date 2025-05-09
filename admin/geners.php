<?php
require_once '../config/db.php';
require_once '../middleware/admin_check.php';
require_once '../utils/response.php';

check_admin(); // Ensure only admins can access

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? null;
$genre_name = $data['name'] ?? null;
$genre_id = $data['id'] ?? null;

try {
    if ($action == 'add' && $genre_name) {
        $stmt = $pdo->prepare("INSERT INTO genres (name) VALUES (?)");
        $stmt->execute([$genre_name]);
        send_response(true, "Genre added");
    }

    if ($action == 'remove' && $genre_id) {
        $stmt = $pdo->prepare("DELETE FROM genres WHERE id = ?");
        $stmt->execute([$genre_id]);
        send_response(true, "Genre removed");
    }

    // Fetch all genres
    if ($action == 'list') {
        $stmt = $pdo->query("SELECT * FROM genres");
        $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        send_response(true, "Genres retrieved", $genres);
    }
} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
