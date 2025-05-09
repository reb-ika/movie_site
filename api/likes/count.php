<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

require_once '../../config/db.php';
require_once '../../api/likes/LikesAPI.php';
require_once '../../middleware/auth_check.php';
$database = new Database();
$db = $database->connect();

$likesAPI = new LikesAPI($db);

// Get target type and ID from query parameters
$likesAPI->target_type = $_GET['target_type'] ?? null;
$likesAPI->target_id = $_GET['target_id'] ?? null;

if (!empty($likesAPI->target_type) && !empty($likesAPI->target_id)) {
    $count = $likesAPI->getLikesCount();
    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Target type and ID are required'
    ]);
}