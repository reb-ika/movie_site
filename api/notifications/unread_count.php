<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/notifications/NotificationsAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$notificationsAPI = new NotificationsAPI($db);

// Authenticate user
$user = authenticate();
$notificationsAPI->user_id = $user['id'];

$count = $notificationsAPI->getUnreadCount();

echo json_encode([
    'success' => true,
    'count' => $count
]);