<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit(json_encode(['success' => false, 'error' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($current_password)) {
        $errors['current_password'] = 'Current password is required';
    }

    if (empty($new_password)) {
        $errors['new_password'] = 'New password is required';
    } elseif (strlen($new_password) < 8) {
        $errors['new_password'] = 'Password must be at least 8 characters';
    }

    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($errors)) {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($current_password, $user['password'])) {
                $errors['current_password'] = 'Current password is incorrect';
            } else {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                $success = true;
            }
        } catch (Exception $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => $success,
    'errors' => $errors
]);