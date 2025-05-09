<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/comments/CommentsAPI.php';

$database = new Database();
$db = $database->connect();

$commentsAPI = new CommentsAPI($db);

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->user_id) && !empty($data->movie_id) && !empty($data->content)) {
    $commentsAPI->user_id = $data->user_id;
    $commentsAPI->movie_id = $data->movie_id;
    $commentsAPI->content = $data->content;
    $commentsAPI->parent_id = isset($data->parent_id) ? $data->parent_id : null;

    if ($commentsAPI->addComment()) {
        echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}
?>
