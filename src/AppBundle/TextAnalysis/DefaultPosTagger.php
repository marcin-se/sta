<?php 

namespace AppBundle\TextAnalysis;

/* 
 * Part of speech tagger.
 *
 * Recognizes the following parts of speech:
 * -adjective
 * -noun (topic)
 * -negation
 * -coma ( which technically is not PoS but i need it tagged as well )
 */

class DefaultPosTagger implements PosTaggerInterface {

    private $topics;
    private $adjectives;
    private $negations;

    public function __construct($topicRepository, $adjectiveRepository, $negationsDictPath = __DIR__ .'/Lexicon/negations_en.txt') {

        $this->topics = $topicRepository->findNames();
        $this->adjectives = $adjectiveRepository->findNames();
        $this->negations = array();

        $fh = fopen($negationsDictPath, 'r');
        while($line = fgets($fh)) {
            $line = trim(preg_replace('/\s+/', ' ', $line)); // removes new line
            array_push($this->negations, $line);
        }
        fclose($fh);
    }

    /**
    * Determines to which group each token in a sentence belongs (topics, adjectives, negations, comas, other).
    * @param string
    * @return AppBundle\TextAnalysis\PosTaggerResult
    **/
    public function tag($setntence) {
        $posTaggerResult = new PosTaggerResult();

        $setntenceNormalized = $this->normalize($setntence);
        $posTaggerResult->setSentenceNormalized($setntenceNormalized);
        
        $posTags = array();
        $setntenceNormalized = explode(' ', $setntenceNormalized);
        foreach($setntenceNormalized as $token) {
            if ($this->matches($token, $this->topics)){
                array_push($posTags, PosEnum::TOPIC);
            }
            else if ($this->matches($token, $this->adjectives)){
                array_push($posTags, PosEnum::ADJ);
            } 
            else if ($this->matches($token, $this->negations, false)){
                array_push($posTags, PosEnum::NEG);
            } 
            else if (strcmp($token, ',') == 0){
                array_push($posTags, PosEnum::COMA);
            }
            else {
                array_push($posTags, PosEnum::OTHER);
            }
        }
        $posTaggerResult->setPosTags($posTags);
        return $posTaggerResult;
    }

    /**
    * Converts all letters to lower case, separates comas 'glued' with words ( i.e good,hotel => good , hotel )
    * @param string
    * @return string
    **/
    public function normalize($text){
        $result = '';
        $text = strtolower($text);

        $exploded = explode(',', $text);
        $i = 0;
        $len = count($exploded);
        foreach($exploded as $token){
            $token = trim($token);
            $result .= $token;
            if ($i != $len - 1) {
                $result .= ' , ';
            }
            $i++;
        }

        return $result;
    }

    /**
    * Checks if a given subject matches to any of predefined sets of words (adjectives, topics, negations).
    **/
    private function matches($subject, $array, $leftBoundary=true){
        foreach($array as $item) {
            $regexp = $leftBoundary ? '/\b'.$item.'/' : '/'.$item.'/';
            if(preg_match($regexp, $subject)){
                return true;
            }
        }
        return false;
    }
}
?>