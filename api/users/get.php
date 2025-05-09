<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../api/users/UsersAPI.php';
require_once '../../middleware/auth_check.php';

function authenticate() {
    // Example implementation of the authenticate function
    // Replace this with your actual authentication logic
    return ['id' => 1]; // Simulated authenticated user with ID 1
}

$database = new Database();
$db = $database->connect();

$userAPI = new UserAPI($db);

// Authenticate
$current_user = authenticate();
$userAPI->id = $current_user['id'];

try {
    $user = $userAPI->getSingleUser();
    if ($user) {
        echo json_encode([
            'success' => true,
            'data' => $user
        ]);
    } else {
        throw new Exception('User not found');
    }
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>