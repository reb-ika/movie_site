<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../api/comments/CommentsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$commentsAPI = new CommentsAPI($db);

// Get movie ID
$commentsAPI->movie_id = $_GET['movie_id'] ?? null;

if (!empty($commentsAPI->movie_id)) {
    try {
        $comments = $commentsAPI->getComments();
        echo json_encode([
            'success' => true,
            'data' => $comments->fetchAll(PDO::FETCH_ASSOC)
        ]);
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
?>