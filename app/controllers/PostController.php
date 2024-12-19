<?php
namespace controllers;

use models\Post;

class PostController {
    private $postModel;

    public function __construct($postModel) {
        $this->postModel = $postModel;
    }

    public function getAllPosts() {
        return $this->postModel->getAllPosts();
    }

    public function getPostById($id) {
        return $this->postModel->getPostById($id);
    }

    public function createPost($title, $content, $author_id, $topic_id, $image_url = null) {
        return $this->postModel->create($title, $content, $author_id, $topic_id, $image_url);
    }

    public function updatePost($id, $title, $content, $topic_id, $image_url = null) {
        return $this->postModel->update($id, $title, $content, $topic_id, $image_url);
    }

    public function deletePost($id, $author_id) {
        return $this->postModel->delete($id, $author_id);
    }

    public function incrementLikes($post_id, $user_id) {
        return $this->postModel->incrementLikes($post_id, $user_id);
    }

    public function decrementLikes($post_id, $user_id) {
        return $this->postModel->decrementLikes($post_id, $user_id);
    }

    public function hasUserLikedPost($post_id, $user_id) {
        return $this->postModel->hasUserLikedPost($post_id, $user_id);
    }

    public function getLikes($id) {
        return $this->postModel->getLikes($id);
    }

    public function getPostsByTopicId($topicId) {
        return $this->postModel->getPostsByTopicId($topicId);
    }

    public function getPostsByUserId($userId) {
        return $this->postModel->getPostsByUserId($userId);
    }
}
?>