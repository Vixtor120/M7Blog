<?php
namespace models;

class Post {
    private $conn;
    private $table_name = "posts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($title, $content, $author_id) {
        $query = "INSERT INTO " . $this->table_name . " (title, content, author_id) VALUES (:title, :content, :author_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author_id', $author_id);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAllPosts() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPostById($id) {
        $query = "SELECT p.*, u.username as author FROM " . $this->table_name . " p JOIN users u ON p.author_id = u.id WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getPostByTitle($title) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE title = :title";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
?>