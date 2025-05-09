<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../../../utils/jwt.php';

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate input
    if (empty($data['email']) || empty($data['password'])) {
        throw new Exception('Email and password are required', 400);
    }

    // Initialize database and user model
    $db = new Database();
    $userModel = new User($db->connect());

    // Attempt login
    $user = $userModel->login($data['email'], $data['password']);
    if (!$user) {
        throw new Exception('Invalid credentials', 401);
    }

    // Generate JWT
    $jwt = generateJWT([
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role']
    ]);

    // Respond with success
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'token' => $jwt,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'avatar' => $user['avatar']
        ]
    ]);

} catch (Exception $e) {
    // Handle errors
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>