<?php
header('Content-Type: application/json');
require_once '../../includes/config/db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['username'], $data['email'], $data['password'])) {
        throw new Exception("All fields are required", 400);
    }

    $username = htmlspecialchars($data['username']);
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    if (!$email) {
        throw new Exception("Invalid email address", 400);
    }

    $db = (new Database())->connect();

    // Check if email or username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
    $stmt->execute(['email' => $email, 'username' => $username]);
    if ($stmt->fetch()) {
        throw new Exception("Email or username already exists", 409);
    }

    // Insert new user
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);

    echo json_encode(["success" => true, "message" => "User registered successfully"]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["error" => $e->getMessage()]);
}
