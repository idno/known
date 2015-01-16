<?php

/**
 * Check that arguements in the query string are normalised into
 * alphabetical order by key
 *   
 */
class QueryArgumentNormalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlAlphabeticallyOrdersQueryStringArguments() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com?a=1&c=3&b=2' => 'http://www.example.com/?a=1&b=2&c=3'
        ));
        
        $this->runInputToExpectedOutputTests();
    }    
}