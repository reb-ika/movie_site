<?php
class WatchlistAPI {
    private $conn;
    private $table = 'watchlist';

    public $id;
    public $user_id;
    public $movie_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add a movie to the watchlist
    public function addToWatchlist() {
        $query = 'INSERT INTO ' . $this->table . ' (user_id, movie_id) VALUES (:user_id, :movie_id)';
        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->movie_id = htmlspecialchars(strip_tags($this->movie_id));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);

        return $stmt->execute();
    }

    // Remove a movie from the watchlist
    public function removeFromWatchlist() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE user_id = :user_id AND movie_id = :movie_id';
        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->movie_id = htmlspecialchars(strip_tags($this->movie_id));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);

        return $stmt->execute();
    }

    // Get all movies in the user's watchlist
    public function getWatchlist() {
        $query = 'SELECT w.id, w.movie_id, m.title, m.description, m.release_year, m.poster_url 
                  FROM ' . $this->table . ' w
                  JOIN movies m ON w.movie_id = m.id
                  WHERE w.user_id = :user_id
                  ORDER BY w.created_at DESC';
        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();
        return $stmt;
    }
}
?>