<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/uwatchlist/WatchlistAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$watchlistAPI = new WatchlistAPI($db);

// Authenticate
$current_user = authenticate();
$watchlistAPI->user_id = $current_user['id'];

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->movie_id)) {
    $watchlistAPI->movie_id = $data->movie_id;

    try {
        if ($watchlistAPI->removeFromWatchlist()) {
            echo json_encode([
                'success' => true,
                'message' => 'Movie removed from watchlist'
            ]);
        } else {
            throw new Exception('Failed to remove movie from watchlist');
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
?>