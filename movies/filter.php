<?php
require_once '../config/db.php';
require_once '../utils/response.php';

$genre = $_GET['genre'] ?? null;
$year = $_GET['year'] ?? null;

$query = "SELECT * FROM movies WHERE 1=1";
$params = [];

if ($genre) {
    $query .= " AND genre = ?";
    $params[] = $genre;
}

if ($year) {
    $query .= " AND YEAR(release_date) = ?";
    $params[] = $year;
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    send_response(true, "Filtered movies fetched", $movies);
} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
