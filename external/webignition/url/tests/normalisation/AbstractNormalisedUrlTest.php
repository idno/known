<?php

abstract class AbstractNormalisedUrlTest extends AbstractUrlTest {
    
    /**
     *
     * @param string $inputUrl
     * @return \webignition\NormalisedUrl\NormalisedUrl 
     */
    protected function newUrl($inputUrl) {
        return new \webignition\NormalisedUrl\NormalisedUrl($inputUrl);
    }
    
    /**
     *
     * @return array
     */
    protected function urls() {        
        return array(
            'complete' => $this->completeUrl(),
            'protocol-relative' => $this->protocolRelativeUrl(),            
            'root-relative' => $this->rootRelativeUrl(),
            'relative' => $this->relativeUrl()            
        );
    } 
    
    
    protected function completeUrl() {
        return str_replace(':80', '', parent::completeUrl());
    }    
    
    /**
     *
     * @return string
     */
    protected function completeUrlQueryString() {
        $queryStringPairs = array(
            urlencode(self::QUERY_KEY_3).'='.urlencode(self::QUERY_VALUE_3),
            urlencode(self::QUERY_KEY_1).'='.urlencode(self::QUERY_VALUE_1),
            urlencode(self::QUERY_KEY_2).'='.urlencode(self::QUERY_VALUE_2)                                   
        );
        
        return implode('&', $queryStringPairs);
    }     
  
    
}