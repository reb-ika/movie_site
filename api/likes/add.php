<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/likes/LikesAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$likesAPI = new LikesAPI($db);

// Authenticate user
$user = authenticate();
$likesAPI->user_id = $user['id'];

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->target_type) && !empty($data->target_id)) {
    $likesAPI->target_type = $data->target_type; // 'movie', 'review', or 'comment'
    $likesAPI->target_id = $data->target_id;

    try {
        if ($likesAPI->addLike()) {
            echo json_encode([
                'success' => true,
                'message' => 'Like added successfully'
            ]);
        } else {
            throw new Exception('Failed to add like');
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
        'message' => 'Target type and ID are required'
    ]);
}