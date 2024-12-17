<?php
namespace models;

class Comment {
    private $conn;
    private $table = 'comments';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($content, $post_id, $user_id) {
        $query = "INSERT INTO " . $this->table . " (content, post_id, user_id) VALUES (:content, :post_id, :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}
?>