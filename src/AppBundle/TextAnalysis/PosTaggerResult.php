<?php

namespace AppBundle\TextAnalysis;

class PosTaggerResult {
	
	private $sentenceNormalized;
	private $sentenceIdx;
	private $posTags;

	public function getSentenceNormalized(){
		return $this->sentenceNormalized;
	}

	public function setSentenceNormalized($sentenceNormalized){
		$this->sentenceNormalized = $sentenceNormalized;
	}

	public function getSentenceIdx(){
		return $this->sentenceIdx;
	}

	public function setSentenceIdx($sentenceIdx){
		$this->sentenceIdx = $sentenceIdx;
	}

	/**
    * @return array of AppBundle\TextAnalysis\PosEnum
    **/
	public function getPosTags(){
		return $this->posTags;
	}

	/**
    * @param array of AppBundle\TextAnalysis\PosEnum
    **/
	public function setPosTags($posTags){
		$this->posTags = $posTags;
	}
}
