<?php
class ReviewsAPI {
    private $conn;
    private $table = 'reviews';

    public $id;
    public $user_id;
    public $movie_id;
    public $content;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new review
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
            SET user_id = :user_id,
                movie_id = :movie_id,
                content = :content';

        $stmt = $this->conn->prepare($query);

        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':movie_id', $this->movie_id);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Update a review
    public function update() {
        $query = 'UPDATE ' . $this->table . ' 
            SET content = :content,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
            AND user_id = :user_id';

        $stmt = $this->conn->prepare($query);

        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Delete a review
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' 
            WHERE id = :id
            AND user_id = :user_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Get single review by ID
    public function getReview() {
        $query = 'SELECT r.*, u.username, u.avatar 
                 FROM ' . $this->table . ' r
                 JOIN users u ON r.user_id = u.id
                 WHERE r.id = ?
                 LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all reviews for a movie
    public function getMovieReviews() {
        $query = 'SELECT r.*, u.username, u.avatar 
                 FROM ' . $this->table . ' r
                 JOIN users u ON r.user_id = u.id
                 WHERE r.movie_id = ?
                 ORDER BY r.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->movie_id);
        $stmt->execute();

        return $stmt;
    }

    // Get all reviews by a user
    public function getUserReviews() {
        $query = 'SELECT r.*, m.title as movie_title 
                 FROM ' . $this->table . ' r
                 JOIN movies m ON r.movie_id = m.id
                 WHERE r.user_id = ?
                 ORDER BY r.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();

        return $stmt;
    }
}