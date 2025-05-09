<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/reviews/ReviewsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$reviewsAPI = new ReviewsAPI($db);

// Get movie ID from query parameters
$reviewsAPI->movie_id = $_GET['movie_id'] ?? null;

if (!empty($reviewsAPI->movie_id)) {
    $stmt = $reviewsAPI->getMovieReviews();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $reviews,
        'count' => count($reviews)
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Movie ID is required'
    ]);
}