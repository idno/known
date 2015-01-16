<?php

/**
 * Check that normalisation ignores scheme case
 *  
 */
class SchemeNormalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlIgnoresSchemeCase() {
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com'  => 'http://www.example.com/',
            'Http://www.example.com'  => 'http://www.example.com/',
            'hTtp://www.example.com'  => 'http://www.example.com/',
            'htTp://www.example.com'  => 'http://www.example.com/',
            'httP://www.example.com'  => 'http://www.example.com/',
            'Http://www.example.com'  => 'http://www.example.com/',
            'HtTp://www.example.com'  => 'http://www.example.com/',
            'HttP://www.example.com'  => 'http://www.example.com/',
            'HTTp://www.example.com'  => 'http://www.example.com/',
            'httP://www.example.com'  => 'http://www.example.com/',
            'htTP://www.example.com'  => 'http://www.example.com/',
            'hTTP://www.example.com'  => 'http://www.example.com/',
            'https://www.example.com' => 'https://www.example.com/',
            'HttpS://www.example.com' => 'https://www.example.com/',
            'hTtps://www.example.com' => 'https://www.example.com/',
            'htTpS://www.example.com' => 'https://www.example.com/',
            'httPs://www.example.com' => 'https://www.example.com/',
            'HttpS://www.example.com' => 'https://www.example.com/',
            'HtTps://www.example.com' => 'https://www.example.com/',
            'HttPS://www.example.com' => 'https://www.example.com/',
            'HTTps://www.example.com' => 'https://www.example.com/',
            'httPS://www.example.com' => 'https://www.example.com/',
            'htTPs://www.example.com' => 'https://www.example.com/',
            'hTTPS://www.example.com' => 'https://www.example.com/'            
        ));
        
        $this->runInputToExpectedOutputTests();
    } 
}