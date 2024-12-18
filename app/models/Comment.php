<?php
namespace models;

class Comment {
    private $conn;
    private $table = 'comments';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($content, $post_id, $user_id, $parent_id = null) {
        $query = "INSERT INTO " . $this->table . " (content, post_id, user_id, parent_id, created_at) VALUES (:content, :post_id, :user_id, :parent_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':parent_id', $parent_id);
        return $stmt->execute();
    }

    public function getCommentsByPostId($post_id) {
        $query = "SELECT c.*, u.username, p.username AS parent_username FROM " . $this->table . " c 
                  JOIN users u ON c.user_id = u.id 
                  LEFT JOIN " . $this->table . " pc ON c.parent_id = pc.id 
                  LEFT JOIN users p ON pc.user_id = p.id 
                  WHERE c.post_id = :post_id 
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete($comment_id, $user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :comment_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $comment_id);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}
?>