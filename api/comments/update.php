<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/comments/CommentsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$commentsAPI = new CommentsAPI($db);

// Authenticate
$current_user = authenticate();
$commentsAPI->user_id = $current_user['id'];

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->content)) {
    $commentsAPI->id = $data->id;
    $commentsAPI->content = $data->content;

    try {
        if ($commentsAPI->updateComment()) {
            echo json_encode([
                'success' => true,
                'message' => 'Comment updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update comment');
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
        'message' => 'Comment ID and content are required'
    ]);
}
?>