<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
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

// Get user's likes
$stmt = $likesAPI->getUserLikes();
$likes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $likes,
    'count' => count($likes)
]);