<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../api/movies/MoviesAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$movieAPI = new MoviesAPI($db);

// Get search parameters
$data = json_decode(file_get_contents("php://input"));
$movieAPI->title = $data->title ?? null;
$movieAPI->release_year = $data->release_year ?? null;

$movies = $movieAPI->searchMovies();
echo json_encode([
    'success' => true,
    'data' => $movies->fetchAll(PDO::FETCH_ASSOC)
]);
?>