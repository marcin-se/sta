<?php

namespace AppBundle\TextAnalysis;

interface DependencyParserInterface {
	
	/** 
 	* Parses dependencies from text previously tagged with a PosTagerInterface.
 	* @param AppBundle\TextAnalysis\PosTaggerResult
 	* @return array of AppBundle\TextAnalysis\DependencyParserResult
 	**/
	public function parse($posTaggerResult);
}