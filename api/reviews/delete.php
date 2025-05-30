<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/reviews/ReviewsAPI.php';
require_once '../../middleware/auth_check.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$reviewsAPI = new ReviewsAPI($db);

// Authenticate user
$user = authenticate();
$reviewsAPI->user_id = $user['id'];

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
    $reviewsAPI->id = $data->id;

    try {
        if ($reviewsAPI->delete()) {
            echo json_encode([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete review');
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
        'message' => 'Review ID is required'
    ]);
}