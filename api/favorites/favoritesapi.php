<?php
class FavoritesAPI {
    private $conn;
    private $table = 'favorites';

    public $id;
    public $user_id;
    public $movie_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add movie to favorites
    public function add() {
        // First check if already favorited
        if ($this->isFavorite()) {
            return false; // Already exists
        }

        $query = 'INSERT INTO ' . $this->table . ' 
            SET user_id = :user_id,
                movie_id = :movie_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Remove movie from favorites
    public function remove() {
        $query = 'DELETE FROM ' . $this->table . ' 
            WHERE user_id = :user_id
            AND movie_id = :movie_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);

        return $stmt->execute();
    }

    // Check if movie is favorited
    public function isFavorite() {
        $query = 'SELECT id FROM ' . $this->table . ' 
            WHERE user_id = :user_id
            AND movie_id = :movie_id
            LIMIT 1';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get all favorite movies for a user
    public function getUserFavorites() {
        $query = 'SELECT f.*, m.title, m.poster_path, m.release_date 
                 FROM ' . $this->table . ' f
                 JOIN movies m ON f.movie_id = m.id
                 WHERE f.user_id = ?
                 ORDER BY f.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    // Count favorites for a movie
    public function countMovieFavorites() {
        $query = 'SELECT COUNT(*) as favorite_count 
                 FROM ' . $this->table . '
                 WHERE movie_id = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->movie_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['favorite_count'];
    }
}