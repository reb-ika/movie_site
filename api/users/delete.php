<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/classes/UserAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$userAPI = new UserAPI($db);

// Authenticate
$current_user = authenticate();
$userAPI->id = $current_user['id'];

try {
    if ($userAPI->delete()) {
        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete user');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>