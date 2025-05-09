<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/classes/FavoritesAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$favoritesAPI = new FavoritesAPI($db);

// Authenticate user
$user = authenticate();
$favoritesAPI->user_id = $user['id'];

// Get movie ID from query parameters
$favoritesAPI->movie_id = $_GET['movie_id'] ?? null;

if (!empty($favoritesAPI->movie_id)) {
    $isFavorite = $favoritesAPI->isFavorite();
    
    echo json_encode([
        'success' => true,
        'is_favorite' => $isFavorite
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Movie ID is required'
    ]);
}