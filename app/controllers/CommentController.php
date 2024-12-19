<?php
namespace controllers;

use models\Comment;

class CommentController {
    private $commentModel;

    public function __construct($commentModel) {
        $this->commentModel = $commentModel;
    }

    public function createComment($content, $post_id, $user_id, $parent_id = null) {
        return $this->commentModel->create($content, $post_id, $user_id, $parent_id);
    }

    public function deleteComment($comment_id, $user_id) {
        return $this->commentModel->delete($comment_id, $user_id);
    }

    public function hasUserLikedComment($comment_id, $user_id) {
        return $this->commentModel->hasUserLikedComment($comment_id, $user_id);
    }

    public function incrementLikes($comment_id, $user_id) {
        return $this->commentModel->incrementLikes($comment_id, $user_id);
    }

    public function decrementLikes($comment_id, $user_id) {
        return $this->commentModel->decrementLikes($comment_id, $user_id);
    }

    public function getLikes($id) {
        return $this->commentModel->getLikes($id);
    }

    public function getRepliesCount($id) {
        return $this->commentModel->getRepliesCount($id);
    }

    public function getCommentsByUserId($userId) {
        return $this->commentModel->getCommentsByUserId($userId);
    }
}