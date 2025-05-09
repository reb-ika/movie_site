<?php
class CommentsAPI {
    private $conn;
    private $table = 'comments';

    public $id;
    public $user_id;
    public $movie_id;
    public $content;
    public $parent_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add a comment
    // Add a comment
public function addComment() {
    // Check if movie_id exists in the movies table
    $check_movie_query = 'SELECT id FROM movies WHERE id = :movie_id';
    $stmt = $this->conn->prepare($check_movie_query);
    $stmt->bindParam(':movie_id', $this->movie_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        throw new Exception('Invalid movie_id. Movie does not exist.');
    }

    // Proceed with adding the comment if movie exists
    $query = 'INSERT INTO ' . $this->table . ' (user_id, movie_id, content, parent_id) 
              VALUES (:user_id, :movie_id, :content, :parent_id)';
    $stmt = $this->conn->prepare($query);

    $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    $this->movie_id = htmlspecialchars(strip_tags($this->movie_id));
    $this->content = htmlspecialchars(strip_tags($this->content));
    $this->parent_id = htmlspecialchars(strip_tags($this->parent_id));

    $stmt->bindParam(':user_id', $this->user_id);
    $stmt->bindParam(':movie_id', $this->movie_id);
    $stmt->bindParam(':content', $this->content);
    $stmt->bindParam(':parent_id', $this->parent_id);

    return $stmt->execute();
}

    // Get comments for a movie
    public function getComments() {
        $query = 'SELECT c.id, c.content, c.parent_id, c.created_at, u.username AS user 
                  FROM ' . $this->table . ' c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.movie_id = :movie_id
                  ORDER BY c.created_at ASC';
        $stmt = $this->conn->prepare($query);

        $this->movie_id = htmlspecialchars(strip_tags($this->movie_id));
        $stmt->bindParam(':movie_id', $this->movie_id);

        $stmt->execute();
        return $stmt;
    }

    // Update a comment
    public function updateComment() {
        $query = 'UPDATE ' . $this->table . ' 
                  SET content = :content 
                  WHERE id = :id AND user_id = :user_id';
        $stmt = $this->conn->prepare($query);

        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Delete a comment
    public function deleteComment() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id AND user_id = :user_id';
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }
}
?>