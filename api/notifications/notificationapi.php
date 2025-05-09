<?php
class NotificationsAPI {
    private $conn;
    private $table = 'notifications';

    public $id;
    public $user_id;
    public $type; // 'like', 'comment', 'review', 'system'
    public $message;
    public $related_id; // ID of related item (movie_id, review_id, etc)
    public $is_read;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new notification
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
            SET user_id = :user_id,
                type = :type,
                message = :message,
                related_id = :related_id';

        $stmt = $this->conn->prepare($query);

        $this->message = htmlspecialchars(strip_tags($this->message));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':related_id', $this->related_id);

        return $stmt->execute();
    }

    // Get user notifications
    public function getUserNotifications($limit = 10) {
        $query = 'SELECT * FROM ' . $this->table . ' 
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT ' . (int)$limit;

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    // Mark notification as read
    public function markAsRead() {
        $query = 'UPDATE ' . $this->table . ' 
            SET is_read = 1
            WHERE id = :id
            AND user_id = :user_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    // Get unread notifications count
    public function getUnreadCount() {
        $query = 'SELECT COUNT(*) as count FROM ' . $this->table . ' 
            WHERE user_id = :user_id
            AND is_read = 0';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    // Delete notification
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' 
            WHERE id = :id
            AND user_id = :user_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }
}