<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../api/uwatchlist/WatchlistAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$watchlistAPI = new WatchlistAPI($db);

// Authenticate
$current_user = authenticate();
$watchlistAPI->user_id = $current_user['id'];

try {
    $watchlist = $watchlistAPI->getWatchlist();
    echo json_encode([
        'success' => true,
        'data' => $watchlist->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>