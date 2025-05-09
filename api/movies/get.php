<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/db.php';
require_once '../../api/movies/MoviesAPI.php';
require_once '../../middleware/auth_check.php';

$database = new Database();
$db = $database->connect();

$movieAPI = new MoviesAPI($db);

$stmt = $movieAPI->getMovies();
$num = $stmt->rowCount();

if ($num > 0) {
    $movies_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $movie_item = array(
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'release_year' => $release_year,
            'poster_url' => $poster_url,
            'uploaded_by' => $uploaded_by,
        );
        array_push($movies_arr, $movie_item);
    }
    echo json_encode($movies_arr);
} else {
    echo json_encode(array('message' => 'No movies found.'));
}
?>
