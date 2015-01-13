<?php

/**
 * Check that multiple trailing slashes are reduced to a single trailing slash
 *   
 */
class MultipleTrailingSlashNormalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlAddsTrailingSlash() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com' => 'http://www.example.com/',
            'http://www.example.com/' => 'http://www.example.com/',
            'http://www.example.com//' => 'http://www.example.com/',
            'http://www.example.com///' => 'http://www.example.com/',
            'http://www.example.com/one/two/' => 'http://www.example.com/one/two/',            
            'http://www.example.com/one/two//' => 'http://www.example.com/one/two/',
            'http://www.example.com//one/two///' => 'http://www.example.com//one/two/',
            'http://www.example.com///one/two///' => 'http://www.example.com///one/two/'
        ));
        
        $this->runInputToExpectedOutputTests();
    } 
}