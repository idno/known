<?php

namespace webignition\NormalisedUrl\Query;

class Query extends \webignition\Url\Query\Query {
    
    
    /**
     *
     * @var \webignition\NormalisedUrl\Query\Normaliser
     */
    private $normaliser = null;
    
    
    /**
     *
     * @return \webignition\Url\Query\Parser 
     */
    protected function parser() {
        if (is_null($this->normaliser)) {
            $this->normaliser = new \webignition\NormalisedUrl\Query\Normaliser($this->getOrigin());
        }
        
        return $this->normaliser;
    }   
    
}