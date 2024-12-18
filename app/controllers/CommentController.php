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
}
?>