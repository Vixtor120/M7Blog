<?php
namespace models;

class Post {
    private $conn;
    private $table_name = "posts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($title, $content, $author_id, $topic_id, $image_url = null) {
        $query = "INSERT INTO " . $this->table_name . " (title, content, author_id, topic_id, image_url) VALUES (:title, :content, :author_id, :topic_id, :image_url)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->bindParam(':topic_id', $topic_id);
        $stmt->bindParam(':image_url', $image_url);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAllPosts() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
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

    public function getPostsByTopicId($topicId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE topic_id = :topic_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':topic_id', $topicId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function incrementLikes($post_id, $user_id) {
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $query = "UPDATE " . $this->table_name . " SET likes = likes + 1 WHERE id = :post_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function decrementLikes($post_id, $user_id) {
        $this->conn->beginTransaction();
        try {
            $query = "DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $query = "UPDATE " . $this->table_name . " SET likes = likes - 1 WHERE id = :post_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function hasUserLikedPost($post_id, $user_id) {
        $query = "SELECT * FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getLikes($id) {
        $query = "SELECT likes FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function update($id, $title, $content, $topic_id, $image_url = null) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, content = :content, topic_id = :topic_id, image_url = :image_url, created_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':topic_id', $topic_id);
        $stmt->bindParam(':image_url', $image_url);
        return $stmt->execute();
    }

    public function delete($id, $author_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND author_id = :author_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':author_id', $author_id);
        return $stmt->execute();
    }
}
?>