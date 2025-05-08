<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
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

// Get input data
$data = json_decode(file_get_contents("php://input"));

// Update fields
if (isset($data->username)) $userAPI->username = $data->username;
if (isset($data->email)) $userAPI->email = $data->email;
if (isset($data->password)) $userAPI->password = $data->password;
if (isset($data->bio)) $userAPI->bio = $data->bio;
if (isset($data->location)) $userAPI->location = $data->location;

// Handle avatar upload


try {
    if ($userAPI->update()) {
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update user');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>