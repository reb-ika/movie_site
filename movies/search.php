<?php
require_once '../config/db.php';
require_once '../utils/response.php';

$search = trim($_GET['q'] ?? '');

if (!$search) {
    send_response(false, "Search query missing", null, 400);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ?");
    $stmt->execute(["%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Search results", $results);
} catch (PDOException $e) {
    send_response(false, "Error searching movies: " . $e->getMessage(), null, 500);
}
