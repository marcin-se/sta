<?php

namespace AppBundle\TextAnalysis;

interface PosTaggerInterface {
	
	/**
    * Determines to which group each token in a sentence belongs (topics, adjectives, negations, other)
    * @return AppBundle\TextAnalysis\PosTaggerResult
    **/
	public function tag($text);
}