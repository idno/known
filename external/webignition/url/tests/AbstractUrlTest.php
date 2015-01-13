<?php

abstract class AbstractUrlTest extends PHPUnit_Framework_TestCase {
    
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';
    const USER = 'user';
    const PASS = 'pass';    
    const HOST = 'www.example.com'; 
    const PORT_REGULAR =  80;
    const PORT_SECURE = 443;
    const PATH_PART_ONE = 'firstPathPart';
    const PATH_PART_TWO = 'secondPathPart';
    const PATH_PART_THREE = 'lastPathPart';
    const PATH_FILENAME = 'example.html';
    const QUERY_KEY_1 = 'query-key-1[]';
    const QUERY_VALUE_1 = 'query-value-1';
    const QUERY_KEY_2 = 'query-key-2';
    const QUERY_VALUE_2 = 'query-value-2';
    const QUERY_KEY_3 = 'query-key-%3c3%3e';
    const QUERY_VALUE_3 = 'query+value+3';
    const FRAGMENT = 'fragment';
    
    const PATH_PART_DELIMITER = '/';
    
    private $inputAndExpectedOutputUrls = array();
    
    protected $urls = array();
    
    public function __construct() {
        $this->urls = $this->urls();
    }
    
    /**
     * 
     * @param string $inputUrl
     * @return \webignition\Url\Url
     */
    abstract protected function newUrl($inputUrl);
    
    /**
     *
     * @param array $inputAndExpectedOutputUrls 
     */  
    protected function setInputAndExpectedOutputUrls($inputAndExpectedOutputUrls) {
        $this->inputAndExpectedOutputUrls = $inputAndExpectedOutputUrls;
    }
    
    protected function runInputToExpectedOutputTests() {        
        foreach ($this->inputAndExpectedOutputUrls as $inputUrl => $expectedOutputUrl) {
            $url = $this->newUrl($inputUrl);
            $this->assertEquals($expectedOutputUrl, (string)$this->newUrl($inputUrl));
        }         
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
    
    
    /**
     *
     * @return string
     */
    protected function completeUrl() {
        return  self::SCHEME_HTTP
                .':'
                .$this->protocolRelativeUrl();
    }
    
    
    /**
     *
     * @return string
     */
    protected function protocolRelativeUrl() {        
        return  '//'
                .self::USER
                .':'
                .self::PASS
                .'@'
                .self::HOST
                .':'
                .self::PORT_REGULAR
                .$this->rootRelativeUrl();
    }    
    

    /**
     *
     * @return string
     */
    protected function rootRelativeUrl() {
        return self::PATH_PART_DELIMITER
               .$this->relativeUrl();
        }
    
    
    /**
     *
     * @return string
     */
    protected function relativeUrl() {
        return $this->completePath()
               .'?'
               .$this->completeUrlQueryString()
               .'#fragment';
    }
    
    /**
     *
     * @return string
     */
    protected function completePath() {
        return implode(self::PATH_PART_DELIMITER, array(
            self::PATH_PART_ONE,
            self::PATH_PART_TWO,
            self::PATH_PART_THREE
        )).self::PATH_PART_DELIMITER.self::PATH_FILENAME;
    }
    
    /**
     *
     * @return string
     */
    protected function completeUrlQueryString() {
        $queryStringPairs = array(
            urlencode(self::QUERY_KEY_1).'='.urlencode(self::QUERY_VALUE_1),
            urlencode(self::QUERY_KEY_2).'='.urlencode(self::QUERY_VALUE_2),
            urlencode(self::QUERY_KEY_3).'='.urlencode(self::QUERY_VALUE_3)            
        );
        
        return implode('&', $queryStringPairs);
    }    
}