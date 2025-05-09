<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/movies/MoviesAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$movieAPI = new MoviesAPI($db);

// Get input data
$data = json_decode(file_get_contents("php://input"));
if (!empty($data->title) && !empty($data->description) && !empty($data->release_year)) {
    $movieAPI->title = $data->title;
    $movieAPI->description = $data->description;
    $movieAPI->release_year = $data->release_year;
    $movieAPI->uploaded_by = $data->uploaded_by ?? null;

    // Handle poster upload
    if (!empty($_FILES['poster'])) {
        $movieAPI->poster_url = $movieAPI->processPoster();
    }

    try {
        if ($movieAPI->create()) {
            echo json_encode([
                'success' => true,
                'message' => 'Movie created successfully'
            ]);
        } else {
            throw new Exception('Failed to create movie');
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
        'message' => 'Incomplete data'
    ]);
}
