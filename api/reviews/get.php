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

// Get review ID from query parameters
$reviewsAPI->id = $_GET['id'] ?? null;

if (!empty($reviewsAPI->id)) {
    $review = $reviewsAPI->getReview();
    
    if ($review) {
        echo json_encode([
            'success' => true,
            'data' => $review
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Review not found'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Review ID is required'
    ]);
}