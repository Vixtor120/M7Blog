<?php
namespace controllers;

use models\Topic;

class TopicController {
    private $topicModel;

    public function __construct($topicModel) {
        $this->topicModel = $topicModel;
    }

    public function createTopic($name) {
        return $this->topicModel->create($name);
    }

    public function getAllTopics() {
        return $this->topicModel->getAllTopics();
    }

    public function getTopicById($topicId) {
        return $this->topicModel->getTopicById($topicId);
    }
}
?>
