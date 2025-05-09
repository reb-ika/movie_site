<?php
require_once '../config/db.php';
require_once '../middleware/admin_check.php'; // Ensure only admins can access
require_once '../utils/response.php';

check_admin(); // Function to ensure user is an admin

// Get general statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
    $total_users = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) AS total_movies FROM movies");
    $total_movies = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) AS total_comments FROM comments");
    $total_comments = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) AS total_reviews FROM reviews");
    $total_reviews = $stmt->fetchColumn();

    $data = [
        'total_users' => $total_users,
        'total_movies' => $total_movies,
        'total_comments' => $total_comments,
        'total_reviews' => $total_reviews
    ];

    send_response(true, "Dashboard data retrieved", $data);

} catch (PDOException $e) {
    send_response(false, "Database error: " . $e->getMessage(), null, 500);
}
