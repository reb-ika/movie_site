<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../NotificationsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$notificationsAPI = new NotificationsAPI($db);

// Authenticate user (only admins or system should create notifications)
$user = authenticate();
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->user_id) && !empty($data->type) && !empty($data->message)) {
    $notificationsAPI->user_id = $data->user_id;
    $notificationsAPI->type = $data->type;
    $notificationsAPI->message = $data->message;
    $notificationsAPI->related_id = $data->related_id ?? null;

    try {
        if ($notificationsAPI->create()) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Notification created'
            ]);
        } else {
            throw new Exception('Failed to create notification');
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
        'message' => 'Required fields: user_id, type, message'
    ]);
}