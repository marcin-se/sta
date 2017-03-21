<?php 

namespace AppBundle\TextAnalysis;

/** 
* Dependency parser implementing the following dependency parsing methods: 
* -word distance
* -intervening material (partially - only comas, no verbs)
* -valency of heads (partially - only for negations)
*
* Works on text sentences previously tagged with a PosTagerInterface.
*
* features:
* -handles multiple adjectives
* -handles negations
* -handles adjectives without a matching topic
**/

class DefaultDependencyParser implements DependencyParserInterface {

    const MAX_NEGATION_DISTANCE = 2; // empirical, arbitrary
   
    /** 
    * Main parsing method
    * @param AppBundle\TextAnalysis\PosTaggerResult
    * @return array of AppBundle\TextAnalysis\DependencyParserResult
    **/
    public function parse($posTaggerResult) {
        $result = array();

        $tags = $posTaggerResult->getPosTags();

        $topicIndexes = array_keys($tags, PosEnum::TOPIC);
        $adjIndexes = array_keys($tags, PosEnum::ADJ);
        $negIndexes = array_keys($tags, PosEnum::NEG);
        $comaIndexes = array_keys($tags, PosEnum::COMA);
        $maxIdx = count($tags);
        $sentenceNormalized = $posTaggerResult->getSentenceNormalized();

        foreach($adjIndexes as $adjectiveIdx) {

            $topicIdx = $this->findTopic($topicIndexes, $adjectiveIdx, $comaIndexes, $maxIdx);
            $negIdx = $this->findNegation($negIndexes, $adjectiveIdx);

            $dpr = new DependencyParserResult();
            $dpr->setAdjectiveIdx($adjectiveIdx);
            $dpr->setTopicIdx($topicIdx);
            $dpr->setNegationIdx($negIdx);
            $dpr->setSentenceNormalized($sentenceNormalized);            

            array_push($result, $dpr);
        }

        return $result;
    }

    /** 
    * @return int
    **/
    private function findTopic($topicIndexes, $adjectiveIdx, $comaIndexes, $maxIdx){
        $minDistance = $maxIdx;
        $minIdx = -1;
        foreach($topicIndexes as $topicIdx) {
            $distanceTmp = abs($topicIdx - $adjectiveIdx);
            if ( $distanceTmp < $minDistance && $this->areSeparated($adjectiveIdx, $topicIdx, $comaIndexes, $maxIdx) == false) {
                $minDistance  = $distanceTmp;
                $minIdx = $topicIdx;
            }
        }
        return $minIdx;
    }

    /** 
    * @return int
    **/
    private function findNegation($negIndexes, $adjectiveIdx){
        $negIdx = -1;
        foreach($negIndexes as $neg) {
            $distanceTmp = $adjectiveIdx - $neg;
            if ( $distanceTmp <= self::MAX_NEGATION_DISTANCE && $distanceTmp > 0) {
                $negIdx  = $neg;
                break;
            }
        }
        return $negIdx;
    }

    /**
    * Check if given tags are not separated by a separator, i.e. coma
    * @param array of separators indexes
    * @return boolean
    **/
    private function areSeparated($tagIdx1, $tagIdx2, $separatorIdxs, $maxIdx){
        array_unshift($separatorIdxs, -1);
        array_push($separatorIdxs, $maxIdx);

        for ($i=0; $i < count($separatorIdxs)-1; $i++){
            if ($this->numberInRange($tagIdx1, $separatorIdxs[$i], $separatorIdxs[$i+1]) &&
                $this->numberInRange($tagIdx2, $separatorIdxs[$i], $separatorIdxs[$i+1])) {
                return false;
            }
        }
        return true;
    }

    private function numberInRange($number, $minRange, $maxRange){
        return ($number > $minRange && $number < $maxRange);
    }
}
