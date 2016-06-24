<?php
/**
 * This class hits Wordnet and fetches synonym for a word
 */

class WordnetApi {
    
    
    private $word;
    
    /**
     * 
     * @param String $word
     * @example echo WordnetApi::_new("happy")->getRandomSynonym();
     */
    function __construct($word="") {
        $this->word = $word;
    }
    
    /**
     * alias of constructor so that you can create and use this class in single line
     * @param type $word
     * @return \WordnetApi
     */
    static function _new($word = ""){
        return new WordnetApi($word);
    }

    /**
     * Returns set of all synonyms
     * @return Array
     */
    public function getSynonyms(){
        $page = file_get_contents("http://wordnetweb.princeton.edu/perl/webwn?s=" . $this->word);
        preg_match_all("/\;s=([a-z]+)/", $page, $matches);
        if(isset($matches[1])){
            return array_unique($matches[1]);
        }
        return array($this->word);
    }
    
    /**
     * Returns a random synonym
     * @return String
     */
    public function getRandomSynonym(){
        $synonyms = $this->getSynonyms();
        return $synonyms[array_rand($synonyms)];
    }
    
    
    
    
    
}

