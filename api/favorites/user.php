<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/favorites/FavoritesAPI.php';
require_once '../../middleware/auth_check.php';
$database = new Database();
$db = $database->connect();

$favoritesAPI = new FavoritesAPI($db);

// Authenticate user
$user = authenticate();
$favoritesAPI->user_id = $user['id'];

// Optional pagination parameters
$page = $_GET['page'] ?? 1;
$perPage = $_GET['per_page'] ?? 10;

$stmt = $favoritesAPI->getUserFavorites();
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $favorites,
    'count' => count($favorites)
]);