<?php

namespace AppBundle\Services;

use AppBundle\Entity\AnalysisResult;
use AppBundle\TextAnalysis\DefaultDependencyParser;
use AppBundle\TextAnalysis\DefaultPosTagger;

class TextAnalysisService {

    private $posTagger;
    private $dependencyParser;    
    private $topics;
    private $adjectives;

    public function __construct($topicRepository, $adjectiveRepository, $posTagger, $dependencyParser) {
        $this->topics = $topicRepository->findAll();
        $this->adjectives = $adjectiveRepository->findAll();
		$this->posTagger = $posTagger;
        $this->dependencyParser = $dependencyParser;		
    }

    /**
    * @param string
    * @return array of AppBundle\TextAnalysis\DependencyParserResult
    **/
    public function getDependencies($text) {
        $result = array();

        $sentences = explode(".", $text);

        foreach ($sentences as $sentence) {
            $tagged = $this->posTagger->tag($sentence);
            $dependencyParserResults = $this->dependencyParser->parse($tagged);
            $result = array_merge($result, $dependencyParserResults);
        }

        return $result;
    }

    /**
    * Matches dependency parser results with topics and adjectives entities
    * @param string
    * @return array of AppBundle\Entity\AnalysisResult
    **/
    public function getAnalysisResults($text) {
    	$analysisResults = array();

    	$dependencyParserResults = $this->getDependencies($text);

		foreach ($dependencyParserResults as $dpr) {

			$tokenized = explode(" ", $dpr->getSentenceNormalized());

			$ar = new AnalysisResult();

			if ($dpr->getTopicIdx() != -1) {
				$topic = $tokenized[$dpr->getTopicIdx()];
				$relatedTopic = $this->match($topic, $this->topics);
				$ar->setTopic($relatedTopic);
			}

			if ($dpr->getAdjectiveIdx() != -1) {
				$adj = $tokenized[$dpr->getAdjectiveIdx()];
				$relatedAdj = $this->match($adj, $this->adjectives);
				$ar->setAdjective($relatedAdj);
			}

			if ($dpr->getNegationIdx() != -1) {
				$ar->setNegated(true);
			}

			array_push($analysisResults, $ar);
	    }

        return $analysisResults;
    }

    private function match($name, $array) {
    	foreach($array as $item) {
    		if (preg_match('/\b'.$item->getName().'/', $name )) {
    			return $item;
    		}
    	}
    }
}