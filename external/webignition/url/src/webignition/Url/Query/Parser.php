<?php

namespace webignition\Url\Query;

class Parser {
    
    const PAIR_DELIMITER = '&';
    const KEY_VALUE_DELIMITER = '=';
   
    /**
     * Supplied URL, unmodified
     * 
     * @var string
     */
    private $origin = null; 
    
    
    /**
     *
     * @var array
     */
    private $keyValuePairs = null;
    
    
    /**
     *
     * @param string $url 
     */
    public function __construct($url) {
        $this->origin = $url;     
    }
    
    
    /**
     *
     * @return array
     */
    public function getKeyValuePairs() {        
        if (is_null($this->keyValuePairs)) {            
            $this->parse();
        }
        
        return $this->keyValuePairs;
    }    
    
        
    private function parse() {
        $pairStrings = explode(self::PAIR_DELIMITER, $this->origin);
        foreach ($pairStrings as $pairString) {            
            $currentPair = explode(self::KEY_VALUE_DELIMITER, $pairString);
            
            $key = urldecode($currentPair[0]);
            $value = isset($currentPair[1]) ? urldecode($currentPair[1]) : null;
            
            $this->keyValuePairs[$key] = $value;            
        }
    }
    
}