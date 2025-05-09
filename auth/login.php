<!-- auth/login.php -->
<div class="auth-container">
    <h2>Login to CineVault</h2>
    <form id="loginForm" class="auth-form">
        <div class="form-group">
            <label for="loginEmail">Email</label>
            <input type="email" id="loginEmail" name="email" required>
        </div>
        <div class="form-group">
            <label for="loginPassword">Password</label>
            <input type="password" id="loginPassword" name="password" required>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
    <div id="loginMessage" class="message-box"></div>
    <p class="auth-link">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<script src="../assets/js/auth.js"></script>
<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/config/db.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['email'], $data['password'])) {
        throw new Exception("Email and password are required", 400);
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = $data['password'];

    if (!$email) {
        throw new Exception("Invalid email address", 400);
    }

    $db = (new Database())->connect();

    // Check user
    $stmt = $db->prepare("SELECT id, username, password FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception("Invalid email or password", 401);
    }

    // Login success
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    echo json_encode(["success" => true, "user" => [
        "id" => $user['id'],
        "username" => $user['username'],
        "email" => $email
    ]]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["error" => $e->getMessage()]);
}
