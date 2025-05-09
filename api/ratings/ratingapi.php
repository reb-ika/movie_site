<?php
class RatingsAPI {
    private $conn;
    private $table = 'ratings';

    public $id;
    public $user_id;
    public $movie_id;
    public $rating;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add or update a rating
    public function addRating() {
        // Check if rating exists
        if ($this->ratingExists()) {
            return $this->updateRating();
        }

        $query = 'INSERT INTO ' . $this->table . ' 
            SET user_id = :user_id, 
                movie_id = :movie_id,
                rating = :rating';

        $stmt = $this->conn->prepare($query);

        $this->rating = min(5, max(1, $this->rating)); // Ensure rating is between 1-5

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);
        $stmt->bindParam(':rating', $this->rating);

        return $stmt->execute();
    }

    // Update existing rating
    public function updateRating() {
        $query = 'UPDATE ' . $this->table . ' 
            SET rating = :rating,
                created_at = CURRENT_TIMESTAMP
            WHERE user_id = :user_id 
            AND movie_id = :movie_id';

        $stmt = $this->conn->prepare($query);

        $this->rating = min(5, max(1, $this->rating)); // Ensure rating is between 1-5

        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);

        return $stmt->execute();
    }

    // Remove a rating
    public function removeRating() {
        $query = 'DELETE FROM ' . $this->table . ' 
            WHERE user_id = :user_id 
            AND movie_id = :movie_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);

        return $stmt->execute();
    }

    // Check if rating exists
    public function ratingExists() {
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

    // Get user's rating for a movie
    public function getUserRating() {
        $query = 'SELECT * FROM ' . $this->table . ' 
            WHERE user_id = :user_id 
            AND movie_id = :movie_id 
            LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get average rating for a movie
    public function getAverageRating() {
        $query = 'SELECT AVG(rating) as average, COUNT(*) as count FROM ' . $this->table . ' 
            WHERE movie_id = :movie_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':movie_id', $this->movie_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}