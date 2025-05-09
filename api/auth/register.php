<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/User.php';

try {
    // Get input data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate input
    $required = ['username', 'email', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("$field is required", 400);
        }
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format', 400);
    }

    if (strlen($data['password']) < 6) {
        throw new Exception('Password must be at least 6 characters', 400);
    }

    // Initialize database and user model
    $db = new Database();
    $userModel = new User($db->connect());

    // Check if user already exists
    if ($userModel->getUserByEmail($data['email'])) {
        throw new Exception('Email already registered', 409);
    }

    // Create user
    $userId = $userModel->createUser([
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => password_hash($data['password'], PASSWORD_BCRYPT) // Secure password hashing
    ]);

    // Respond with success
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'user_id' => $userId
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