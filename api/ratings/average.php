<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/db.php';
require_once '../../api/ratings/RatingsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$ratingsAPI = new RatingsAPI($db);

// Get movie ID from query parameters
$ratingsAPI->movie_id = $_GET['movie_id'] ?? null;

if (!empty($ratingsAPI->movie_id)) {
    $ratingData = $ratingsAPI->getAverageRating();
    echo json_encode([
        'success' => true,
        'average' => round($ratingData['average'], 1),
        'count' => $ratingData['count']
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Movie ID is required'
    ]);
}