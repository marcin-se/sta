<?php

namespace AppBundle\Tests\Mock;

class TopicRepositoryMock {

    private $topicNames;

    public function __construct($topicNames) {
        $this->topicNames = $topicNames;
    }

    public function findNames(){
        return $this->topicNames;
    }
}