<?php
class MoviesAPI {
    private $conn;
    private $table = 'movies';

    public $id;
    public $title;
    public $description;
    public $release_year;
    public $poster_url;
    public $uploaded_by;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all movies
    public function getMovies() {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get a single movie
    public function getSingleMovie() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Search movies
    public function searchMovies() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE 1=1';

        if (!empty($this->title)) {
            $query .= ' AND title LIKE :title';
        }
        if (!empty($this->release_year)) {
            $query .= ' AND release_year = :release_year';
        }

        $query .= ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);

        if (!empty($this->title)) {
            $this->title = '%' . htmlspecialchars(strip_tags($this->title)) . '%';
            $stmt->bindParam(':title', $this->title);
        }
        if (!empty($this->release_year)) {
            $stmt->bindParam(':release_year', $this->release_year);
        }

        $stmt->execute();
        return $stmt;
    }

    // Create a new movie
    public function create() {
        $this->poster_url = $this->processPoster();

        $query = 'INSERT INTO ' . $this->table . ' 
            SET title = :title, description = :description, release_year = :release_year, 
                poster_url = :poster_url, uploaded_by = :uploaded_by';

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->release_year = htmlspecialchars(strip_tags($this->release_year));
        $this->uploaded_by = htmlspecialchars(strip_tags($this->uploaded_by));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':release_year', $this->release_year);
        $stmt->bindParam(':poster_url', $this->poster_url);
        $stmt->bindParam(':uploaded_by', $this->uploaded_by);

        return $stmt->execute();
    }

    // Update a movie
    public function update() {
        $query = 'UPDATE ' . $this->table . ' 
                  SET title = :title, description = :description, release_year = :release_year';

        if (!empty($this->poster_url)) {
            $query .= ', poster_url = :poster_url';
        }

        $query .= ' WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->release_year = htmlspecialchars(strip_tags($this->release_year));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':release_year', $this->release_year);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->poster_url)) {
            $stmt->bindParam(':poster_url', $this->poster_url);
        }

        return $stmt->execute();
    }

    // Delete a movie
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Process poster upload
    public function processPoster() {
        if (!isset($_FILES['poster']) || $_FILES['poster']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $targetDir = '../../uploads/posters/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES['poster']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
            return $fileName;
        }

        return null;
    }
}
?>