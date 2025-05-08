<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Fetch current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $website = trim($_POST['website'] ?? '');

    // Validate inputs
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors['username'] = 'Username must be between 3 and 50 characters';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (strlen($bio) > 500) {
        $errors['bio'] = 'Bio cannot exceed 500 characters';
    }

    // Process avatar upload if provided
    $avatar_path = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatar = $_FILES['avatar'];
        
        // Validate image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($avatar['type'], $allowed_types)) {
            $errors['avatar'] = 'Only JPG, PNG, and GIF images are allowed';
        } elseif ($avatar['size'] > $max_size) {
            $errors['avatar'] = 'Image size must be less than 2MB';
        } else {
            // Generate unique filename
            $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = 'uploads/avatars/' . $filename;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/avatars')) {
                mkdir('uploads/avatars', 0755, true);
            }
            
            // Move uploaded file
            if (move_uploaded_file($avatar['tmp_name'], $upload_path)) {
                // Delete old avatar if it's not the default
                if ($avatar_path && $avatar_path !== 'images/default-avatar.jpg' && file_exists($avatar_path)) {
                    unlink($avatar_path);
                }
                $avatar_path = $upload_path;
            } else {
                $errors['avatar'] = 'Failed to upload image';
            }
        }
    }

    // Check if username or email already exists (if changed)
    if (empty($errors)) {
        try {
            // Check username
            if ($username !== $user['username']) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt->execute([$username, $user_id]);
                if ($stmt->fetch()) {
                    $errors['username'] = 'Username already taken';
                }
            }

            // Check email
            if ($email !== $user['email']) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $user_id]);
                if ($stmt->fetch()) {
                    $errors['email'] = 'Email already in use';
                }
            }
        } catch (Exception $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
        }
    }

    // Update profile if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET username = ?, email = ?, bio = ?, location = ?, website = ?, avatar = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $username,
                $email,
                $bio,
                $location,
                $website,
                $avatar_path,
                $user_id
            ]);

            // Update session username if changed
            if ($username !== $user['username']) {
                $_SESSION['username'] = $username;
            }

            $success = true;
            
            // Fetch updated user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $errors['database'] = 'Failed to update profile: ' . $e->getMessage();
        }
    }
}

// Return JSON response for AJAX or API usage
header('Content-Type: application/json');
echo json_encode([
    'success' => $success,
    'errors' => $errors,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'avatar' => $user['avatar'],
        'bio' => $user['bio'],
        'location' => $user['location'],
        'website' => $user['website']
    ]
]);