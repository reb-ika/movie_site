<?php
session_start();
require_once 'config/database.php';

// Set JSON header for API response
header('Content-Type: application/json');

// Check if user is logged in (optional - depends on your requirements)
$current_user_id = $_SESSION['user_id'] ?? null;

try {
    // Get requested profile ID from URL
    $profile_id = isset($_GET['id']) ? intval($_GET['id']) : $current_user_id;
    
    if (!$profile_id) {
        throw new Exception("Profile ID not specified and no user logged in", 400);
    }

    // Fetch user profile data
    $stmt = $pdo->prepare("
        SELECT 
            id, username, email, avatar, bio, location, website, 
            role, created_at, updated_at
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$profile_id]);
    $profile_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile_data) {
        throw new Exception("User not found", 404);
    }

    // Fetch statistics
    $stats = [
        'watchlist_count' => 0,
        'favorites_count' => 0,
        'ratings_count' => 0,
        'uploaded_movies_count' => 0,
        'reviews_count' => 0
    ];

    // Count watchlist items
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM watchlist WHERE user_id = ?");
    $stmt->execute([$profile_id]);
    $stats['watchlist_count'] = $stmt->fetchColumn();

    // Count favorites
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
    $stmt->execute([$profile_id]);
    $stats['favorites_count'] = $stmt->fetchColumn();

    // Count ratings
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ratings WHERE user_id = ?");
    $stmt->execute([$profile_id]);
    $stats['ratings_count'] = $stmt->fetchColumn();

    // Count uploaded movies (if user is an uploader/admin)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM movies WHERE uploaded_by = ?");
    $stmt->execute([$profile_id]);
    $stats['uploaded_movies_count'] = $stmt->fetchColumn();

    // Count reviews
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ?");
    $stmt->execute([$profile_id]);
    $stats['reviews_count'] = $stmt->fetchColumn();

    // Fetch recent activity
    $recent_activity = [];

    // Get 5 most recent uploaded movies
    $stmt = $pdo->prepare("
        SELECT id, title, poster_url, release_year 
        FROM movies 
        WHERE uploaded_by = ? 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$profile_id]);
    $recent_activity['uploaded_movies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get 5 most recent reviews with movie info
    $stmt = $pdo->prepare("
        SELECT r.id, r.content, r.created_at, m.id AS movie_id, m.title, m.poster_url
        FROM reviews r
        JOIN movies m ON r.movie_id = m.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$profile_id]);
    $recent_activity['reviews'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get 5 most recent ratings with movie info
    $stmt = $pdo->prepare("
        SELECT rt.rating, rt.created_at, m.id AS movie_id, m.title, m.poster_url
        FROM ratings rt
        JOIN movies m ON rt.movie_id = m.id
        WHERE rt.user_id = ?
        ORDER BY rt.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$profile_id]);
    $recent_activity['ratings'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare final response
    $response = [
        'success' => true,
        'profile' => $profile_data,
        'stats' => $stats,
        'recent_activity' => $recent_activity,
        'is_current_user' => ($current_user_id === $profile_id),
        'timestamp' => time()
    ];

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode() ?: 500
    ];
}

// Output JSON response
echo json_encode($response, JSON_PRETTY_PRINT);