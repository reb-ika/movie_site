<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';
require_once '../../api/auth/AuthAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$authAPI = new AuthAPI($db);

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->email) && !empty($data->password)) {
    $authAPI->username = $data->username;
    $authAPI->email = $data->email;
    $authAPI->password = $data->password;

    try {
        if ($authAPI->register()) {
            echo json_encode([
                'success' => true,
                'message' => 'User registered successfully'
            ]);
        } else {
            throw new Exception('Failed to register user');
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
        'message' => 'All fields are required'
    ]);
}
?>