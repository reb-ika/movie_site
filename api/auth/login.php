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

if (!empty($data->email) && !empty($data->password)) {
    $authAPI->email = $data->email;
    $authAPI->password = $data->password;

    try {
        $user = $authAPI->login();
        if ($user) {
            // Start session or generate token
            session_start();
            $_SESSION['user_id'] = $user['id'];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            throw new Exception('Invalid email or password');
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required'
    ]);
}
?>