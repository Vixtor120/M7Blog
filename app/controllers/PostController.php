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

    public function createPost($title, $content, $author_id) {
        return $this->postModel->create($title, $content, $author_id);
    }
}
?>