<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../../api/auth/AuthAPI.php';
require_once '../../middleware/auth_check.php';


if (isset($_SESSION['user_id'])) {
    session_destroy();
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful'
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No active session found'
    ]);
}
?>