<?php

namespace AppBundle\Tests\TextAnalysis;

use AppBundle\TextAnalysis\DefaultPosTagger;
use AppBundle\TextAnalysis\PosEnum;

use AppBundle\Tests\Mock\TopicRepositoryMock;
use AppBundle\Tests\Mock\AdjectiveRepositoryMock;

class PosTaggerTest extends \PHPUnit_Framework_TestCase {	

    public function testDefaultPosTagger_normalize() {
        $topicRepoMock = new TopicRepositoryMock(array());
        $adjRepoMock = new AdjectiveRepositoryMock(array());
        $tagger = new DefaultPosTagger($topicRepoMock, $adjRepoMock);
        $normalized = $tagger->normalize('Terrible,old, not ,quite clean.');
        $this->assertEquals('terrible , old , not , quite clean.', $normalized);
    }

    public function testDefaultPosTagger_negation_coma_plural_multipleAdjectives() {
        $topics = ['fox', 'dog'];
        $adjectives = ['quick', 'brown', 'lazy'];
        $topicRepoMock = new TopicRepositoryMock($topics);
        $adjRepoMock = new AdjectiveRepositoryMock($adjectives);

        $tagger = new DefaultPosTagger($topicRepoMock, $adjRepoMock);

        $posTaggerResult = $tagger->tag('The quick brown foxes,wouldn\'t jump over the not lazy dogs.', 0);

        $this->assertEquals($posTaggerResult->getSentenceNormalized(), 'the quick brown foxes , wouldn\'t jump over the not lazy dogs.'); // the
        
        $tags = $posTaggerResult->getPosTags();

        // in a longer term i would probably implement some helper method to handle these assertions...
        $this->assertEquals($tags[0], PosEnum::OTHER); // the
        $this->assertEquals($tags[1], PosEnum::ADJ); // quick
        $this->assertEquals($tags[2], PosEnum::ADJ); // brown 
        $this->assertEquals($tags[3], PosEnum::TOPIC); // foxes
        $this->assertEquals($tags[4], PosEnum::COMA); // ,
        $this->assertEquals($tags[5], PosEnum::NEG); // wouldn't
        $this->assertEquals($tags[6], PosEnum::OTHER); // jump
        $this->assertEquals($tags[7], PosEnum::OTHER); // over
        $this->assertEquals($tags[8], PosEnum::OTHER); // the
        $this->assertEquals($tags[9], PosEnum::NEG); // not
        $this->assertEquals($tags[10], PosEnum::ADJ); // lazy
        $this->assertEquals($tags[11], PosEnum::TOPIC); // dog
    }
}
