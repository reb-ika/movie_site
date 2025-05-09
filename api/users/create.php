<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/users/UsersAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$userAPI = new UserAPI($db);

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->email) && !empty($data->password)) {
    $userAPI->username = $data->username;
    $userAPI->email = $data->email;
    $userAPI->password = $data->password;
    $userAPI->bio = $data->bio ?? null;
    $userAPI->location = $data->location ?? null;

    // Handle avatar upload
    

    try {
        if ($userAPI->create()) {
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully'
            ]);
        } else {
            throw new Exception('Failed to create user');
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
        'message' => 'Incomplete data'
    ]);
}
?>