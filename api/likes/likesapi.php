<?php
class LikesAPI {
    private $conn;
    private $table = 'likes';

    public $id;
    public $user_id;
    public $target_type;
    public $target_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add a like
    public function addLike() {
        // Check if like already exists
        if ($this->likeExists()) {
            throw new Exception('Like already exists');
        }

        $query = 'INSERT INTO ' . $this->table . ' 
            SET user_id = :user_id, 
                target_type = :target_type,
                target_id = :target_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':target_type', $this->target_type);
        $stmt->bindParam(':target_id', $this->target_id);

        return $stmt->execute();
    }

    // Remove a like
    public function removeLike() {
        $query = 'DELETE FROM ' . $this->table . ' 
            WHERE user_id = :user_id 
            AND target_type = :target_type 
            AND target_id = :target_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':target_type', $this->target_type);
        $stmt->bindParam(':target_id', $this->target_id);

        return $stmt->execute();
    }

    // Check if like exists
    public function likeExists() {
        $query = 'SELECT id FROM ' . $this->table . ' 
            WHERE user_id = :user_id 
            AND target_type = :target_type 
            AND target_id = :target_id 
            LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':target_type', $this->target_type);
        $stmt->bindParam(':target_id', $this->target_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get likes count for a target
    public function getLikesCount() {
        $query = 'SELECT COUNT(*) as like_count FROM ' . $this->table . ' 
            WHERE target_type = :target_type 
            AND target_id = :target_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':target_type', $this->target_type);
        $stmt->bindParam(':target_id', $this->target_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];
    }

    // Get user's likes
    public function getUserLikes() {
        $query = 'SELECT * FROM ' . $this->table . ' 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt;
    }
}