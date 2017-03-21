<?php

namespace AppBundle\TextAnalysis;

use JsonSerializable;

class DependencyParserResult implements JsonSerializable {

	private $topicIdx;
	private $adjectiveIdx;
	private $negationIdx;
	private $sentenceIdx;
	private $sentenceNormalized;

	public function getTopicIdx(){
		return $this->topicIdx;
	}

	public function setTopicIdx($topicIdx){
		$this->topicIdx = $topicIdx;
	}

	public function getAdjectiveIdx(){
		return $this->adjectiveIdx;
	}

	public function setAdjectiveIdx($adjectiveIdx){
		$this->adjectiveIdx = $adjectiveIdx;
	}

	public function getNegationIdx(){
		return $this->negationIdx;
	}

	public function setNegationIdx($negationIdx){
		$this->negationIdx = $negationIdx;
	}

	public function getSentenceIdx(){
		return $this->sentenceIdx;
	}

	public function setSentenceIdx($sentenceIdx){
		$this->sentenceIdx = $sentenceIdx;
	}

	public function getSentenceNormalized(){
		return $this->sentenceNormalized;
	}

	public function setSentenceNormalized($sentenceNormalized){
		$this->sentenceNormalized = $sentenceNormalized;
	}

	public function jsonSerialize() {
        return ['topicIdx' => $this->getTopicIdx(),
        		'adjIdx' => $this->getAdjectiveIdx(),
        		'negIdx' => $this->getNegationIdx(),
        		'sentenceIdx' => $this->getSentenceIdx(),
        		'sentenceNormalized' => $this->getSentenceNormalized()
        		];
    }
}
