<?php

namespace AppBundle\Tests\Mock;

class AdjectiveRepositoryMock {

    private $adjectiveNames;

    public function __construct($adjectiveNames) {
        $this->adjectiveNames = $adjectiveNames;
    }

    public function findNames(){
        return $this->adjectiveNames;
    }
}