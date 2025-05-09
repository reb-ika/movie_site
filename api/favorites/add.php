<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
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

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->movie_id)) {
    $favoritesAPI->movie_id = $data->movie_id;

    try {
        if ($favoritesAPI->add()) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Movie added to favorites',
                'favorite_id' => $favoritesAPI->id
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Movie was already in favorites'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Movie ID is required'
    ]);
}