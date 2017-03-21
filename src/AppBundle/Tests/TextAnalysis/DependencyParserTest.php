<?php

namespace AppBundle\Tests\TextAnalysis;

use AppBundle\TextAnalysis\DefaultPosTagger;
use AppBundle\TextAnalysis\DefaultDependencyParser;
use AppBundle\TextAnalysis\DependencyParserResult;
use AppBundle\TextAnalysis\PosEnum;

use AppBundle\Tests\Mock\TopicRepositoryMock;
use AppBundle\Tests\Mock\AdjectiveRepositoryMock;

class DependencyParserTest extends \PHPUnit_Framework_TestCase {	

    private function setupPosTagger($topics, $adjectives){
        $topicRepoMock = new TopicRepositoryMock($topics);
        $adjRepoMock = new AdjectiveRepositoryMock($adjectives);
        return new DefaultPosTagger($topicRepoMock, $adjRepoMock);
    }
    
    public function testDependencyParser_comas_multipleDetachedAdjectives() {
        $topics = ['shower'];
        $adjectives = ['terrible', 'old', 'tiny'];
        $tagger = $this->setupPosTagger($topics, $adjectives);
        $posTaggerResult = $tagger->tag('Terrible , old , tiny shower.', 0);

        $parser = new DefaultDependencyParser();
        $dependencyParserResults = $parser->parse($posTaggerResult);

        $this->assertEquals(count($dependencyParserResults), 3);

        $this->assertEquals($dependencyParserResults[0]->getTopicIdx(), -1); // terrible is separated by coma, it should not be matched to shower
        $this->assertEquals($dependencyParserResults[0]->getAdjectiveIdx(), 0); // terrible
        $this->assertEquals($dependencyParserResults[0]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[1]->getTopicIdx(), -1); // old is also separated by coma, it should not be matched to shower
        $this->assertEquals($dependencyParserResults[1]->getAdjectiveIdx(), 2); // old
        $this->assertEquals($dependencyParserResults[1]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[2]->getTopicIdx(), 5); // shower and tiny are in the same subsentence, should go together
        $this->assertEquals($dependencyParserResults[2]->getAdjectiveIdx(), 4); // tiny
        $this->assertEquals($dependencyParserResults[2]->getNegationIdx(), -1); // no negation
    }

    public function testDependencyParser_negation_plural_multipleAdjectives() {
        $topics = ['fox', 'dog'];
        $adjectives = ['quick', 'brown', 'lazy'];
        $tagger = $this->setupPosTagger($topics, $adjectives);
        $posTaggerResult = $tagger->tag('The quick brown foxes wouldn\'t jump over the not particulary lazy dogs', 0);

        $parser = new DefaultDependencyParser();
        $dependencyParserResults = $parser->parse($posTaggerResult);
        
        $this->assertEquals(count($dependencyParserResults), 3);

        $this->assertEquals($dependencyParserResults[0]->getTopicIdx(), 3); // fox
        $this->assertEquals($dependencyParserResults[0]->getAdjectiveIdx(), 1); // quick
        $this->assertEquals($dependencyParserResults[0]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[1]->getTopicIdx(), 3); // fox        
        $this->assertEquals($dependencyParserResults[1]->getAdjectiveIdx(), 2); // brown
        $this->assertEquals($dependencyParserResults[0]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[2]->getTopicIdx(), 11); // dog        
        $this->assertEquals($dependencyParserResults[2]->getNegationIdx(), 8); // not
        $this->assertEquals($dependencyParserResults[2]->getAdjectiveIdx(), 10); // lazy

    }
    
    public function testDependencyParser_negation_uppercase_detachedAdjectives() {
        $topics = ['fox', 'dog'];
        $adjectives = ['terrible', 'old', 'clean'];
        $tagger = $this->setupPosTagger($topics, $adjectives);
        $posTaggerResult = $tagger->tag('Terrible , old , not quite clean.', 0);

        $parser = new DefaultDependencyParser();
        $dependencyParserResults = $parser->parse($posTaggerResult, 0);

        $this->assertEquals(count($dependencyParserResults), 3);

        $this->assertEquals($dependencyParserResults[0]->getAdjectiveIdx(), 0); // terrible
        $this->assertEquals($dependencyParserResults[0]->getTopicIdx(), -1); // no topic
        $this->assertEquals($dependencyParserResults[0]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[1]->getAdjectiveIdx(), 2); // old
        $this->assertEquals($dependencyParserResults[1]->getTopicIdx(), -1); // no topic
        $this->assertEquals($dependencyParserResults[1]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[2]->getNegationIdx(), 4); // not
        $this->assertEquals($dependencyParserResults[2]->getAdjectiveIdx(), 6); // clean
        $this->assertEquals($dependencyParserResults[2]->getTopicIdx(), -1); // no topic
    }

    public function testDependencyParser_coma_uppercase_plural_multipleAdjectives() {
        $topics = ['hotel', 'room'];
        $adjectives = ['new', 'modern', 'great'];
        $tagger = $this->setupPosTagger($topics, $adjectives);
        $posTaggerResult = $tagger->tag('Hotel itself is very new & modern , rooms were great.', 0);

        $parser = new DefaultDependencyParser();
        $dependencyParserResults = $parser->parse($posTaggerResult, 0);

        $this->assertEquals($dependencyParserResults[0]->getTopicIdx(), 0); // hotel
        $this->assertEquals($dependencyParserResults[0]->getAdjectiveIdx(), 4); // new
        $this->assertEquals($dependencyParserResults[0]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[1]->getTopicIdx(), 0); // hotel
        $this->assertEquals($dependencyParserResults[1]->getAdjectiveIdx(), 6); // modern
        $this->assertEquals($dependencyParserResults[1]->getNegationIdx(), -1); // no negation

        $this->assertEquals($dependencyParserResults[2]->getTopicIdx(), 8); // room
        $this->assertEquals($dependencyParserResults[2]->getAdjectiveIdx(), 10); // modern
        $this->assertEquals($dependencyParserResults[2]->getNegationIdx(), -1); // no negation
    }

    public function testDependencyParser_regexp() {
        $topics = ['he', 'shower'];
        $adjectives = ['best'];
        $tagger = $this->setupPosTagger($topics, $adjectives);
        $posTaggerResult = $tagger->tag('comfortable beds & possibly the best shower ever!', 0);

        $parser = new DefaultDependencyParser();
        $dependencyParserResults = $parser->parse($posTaggerResult, 0);

        $this->assertEquals($dependencyParserResults[0]->getAdjectiveIdx(), 5); // best
        $this->assertEquals($dependencyParserResults[0]->getTopicIdx(), 6); // shower
        $this->assertEquals($dependencyParserResults[0]->getNegationIdx(), -1); // no negation
    }
}
