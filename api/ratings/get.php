<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/ratings/RatingsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$ratingsAPI = new RatingsAPI($db);

// Authenticate user if getting user-specific rating
if (isset($_GET['user_rating'])) {
    $user = authenticate();
    $ratingsAPI->user_id = $user['id'];
}

// Get movie ID from query parameters
$ratingsAPI->movie_id = $_GET['movie_id'] ?? null;

if (!empty($ratingsAPI->movie_id)) {
    if (isset($_GET['user_rating'])) {
        // Get user's specific rating
        $rating = $ratingsAPI->getUserRating();
        echo json_encode([
            'success' => true,
            'data' => $rating ?: null
        ]);
    } else {
        // Get all ratings for movie
        // (You might want to implement this method in RatingsAPI if needed)
        http_response_code(501);
        echo json_encode([
            'success' => false,
            'message' => 'Not implemented'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Movie ID is required'
    ]);
}