<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/movies/MoviesAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$movieAPI = new MoviesAPI($db);

// Get input data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
    $movieAPI->id = $data->id;
    $movieAPI->title = $data->title ?? null;
    $movieAPI->description = $data->description ?? null;
    $movieAPI->release_year = $data->release_year ?? null;

    // Handle poster upload
    if (!empty($_FILES['poster'])) {
        $movieAPI->poster_url = $movieAPI->processPoster();
    }

    try {
        if ($movieAPI->update()) {
            echo json_encode([
                'success' => true,
                'message' => 'Movie updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update movie');
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