<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
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

if (!empty($data->id)) {
    $commentsAPI->id = $data->id;

    try {
        if ($commentsAPI->deleteComment()) {
            echo json_encode([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete comment');
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
        'message' => 'Comment ID is required'
    ]);
}
?>