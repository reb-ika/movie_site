<?php
require_once '../config/db.php';
require_once '../utils/response.php';
require_once '../middleware/auth_check.php';

require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$genre = trim($data['genre'] ?? '');
$release_date = $data['release_date'] ?? '';
$poster_path = $data['poster_path'] ?? null;
$created_by = get_authenticated_user_id();

if (!$title || !$genre || !$release_date) {
    send_response(false, "Missing required fields", null, 400);
}

try {
    $stmt = $pdo->prepare("INSERT INTO movies (title, description, genre, release_date, poster_path, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $genre, $release_date, $poster_path, $created_by]);

    send_response(true, "Movie created successfully", ["id" => $pdo->lastInsertId()], 201);
} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
