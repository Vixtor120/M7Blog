<?php
namespace models;

class Comment {
    private $conn;
    private $table = 'comments';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($content, $post_id, $user_id, $parent_id = null) {
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO " . $this->table . " (content, post_id, user_id, parent_id, created_at) VALUES (:content, :post_id, :user_id, :parent_id, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':parent_id', $parent_id);
            $stmt->execute();

            if ($parent_id) {
                $query = "UPDATE " . $this->table . " SET replies_count = replies_count + 1 WHERE id = :parent_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':parent_id', $parent_id);
                $stmt->execute();
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getCommentsByPostId($post_id) {
        $query = "SELECT c.*, u.username, p.username AS parent_username FROM " . $this->table . " c 
                  JOIN users u ON c.user_id = u.id 
                  LEFT JOIN " . $this->table . " pc ON c.parent_id = pc.id 
                  LEFT JOIN users p ON pc.user_id = p.id 
                  WHERE c.post_id = :post_id 
                  ORDER BY c.likes DESC, c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCommentsByUserId($userId) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :userId ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete($comment_id, $user_id) {
        $this->conn->beginTransaction();
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :comment_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comment_id', $comment_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $query = "UPDATE " . $this->table . " SET replies_count = replies_count - 1 WHERE id = (SELECT parent_id FROM " . $this->table . " WHERE id = :comment_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comment_id', $comment_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function incrementLikes($comment_id, $user_id) {
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comment_id', $comment_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $query = "UPDATE " . $this->table . " SET likes = likes + 1 WHERE id = :comment_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comment_id', $comment_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function decrementLikes($comment_id, $user_id) {
        $this->conn->beginTransaction();
        try {
            $query = "DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comment_id', $comment_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $query = "UPDATE " . $this->table . " SET likes = likes - 1 WHERE id = :comment_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comment_id', $comment_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function hasUserLikedComment($comment_id, $user_id) {
        $query = "SELECT * FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $comment_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getRepliesCount($comment_id) {
        $query = "SELECT replies_count FROM " . $this->table . " WHERE id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $comment_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>