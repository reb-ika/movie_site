<?php
class UserAPI {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $bio;
    public $location;
    public $avatar;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all users
    public function getUsers() {
        $query = 'SELECT id, username, email, bio, location, avatar, created_at FROM ' . $this->table . ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get a single user
    public function getSingleUser() {
        $query = 'SELECT id, username, email, bio, location, avatar, created_at FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Search users
    public function searchUsers() {
        $query = 'SELECT id, username, email, bio, location, avatar, created_at FROM ' . $this->table . ' WHERE 1=1';

        if (!empty($this->username)) {
            $query .= ' AND username LIKE :username';
        }
        if (!empty($this->email)) {
            $query .= ' AND email LIKE :email';
        }

        $query .= ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);

        if (!empty($this->username)) {
            $this->username = '%' . htmlspecialchars(strip_tags($this->username)) . '%';
            $stmt->bindParam(':username', $this->username);
        }
        if (!empty($this->email)) {
            $this->email = '%' . htmlspecialchars(strip_tags($this->email)) . '%';
            $stmt->bindParam(':email', $this->email);
        }

        $stmt->execute();
        return $stmt;
    }

    // Create a new user
    public function create() {
        $this->avatar = $this->processAvatar();

        $query = 'INSERT INTO ' . $this->table . ' 
            SET username = :username, email = :email, password = :password, bio = :bio, location = :location, avatar = :avatar';

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->location = htmlspecialchars(strip_tags($this->location));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':avatar', $this->avatar);

        return $stmt->execute();
    }

    // Update user details
    public function update() {
        $query = 'UPDATE ' . $this->table . ' 
                  SET username = :username, email = :email, bio = :bio, location = :location';

        if (!empty($this->password)) {
            $query .= ', password = :password';
        }
        if (!empty($this->avatar)) {
            $query .= ', avatar = :avatar';
        }

        $query .= ' WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $this->password);
        }
        if (!empty($this->avatar)) {
            $stmt->bindParam(':avatar', $this->avatar);
        }

        return $stmt->execute();
    }

    // Delete a user
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Process avatar upload
    private function processAvatar() {
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $targetDir = '../../uploads/avatars/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES['avatar']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
            return $fileName;
        }

        return null;
    }
}
?>