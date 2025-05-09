<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/ratings/RatingsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$ratingsAPI = new RatingsAPI($db);

// Authenticate user
$user = authenticate();
$ratingsAPI->user_id = $user['id'];

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->movie_id)) {
    $ratingsAPI->movie_id = $data->movie_id;

    try {
        if ($ratingsAPI->removeRating()) {
            echo json_encode([
                'success' => true,
                'message' => 'Rating removed successfully'
            ]);
        } else {
            throw new Exception('Failed to remove rating');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Movie ID is required'
    ]);
}