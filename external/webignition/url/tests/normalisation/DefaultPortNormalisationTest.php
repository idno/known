<?php

/**
 * Check that normailsation removes the default port if present
 * http://www.example.com:80/bar.html => http://www.example.com/bar.html
 *   
 */
class DefaultPortNormalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormlisedUrlRemovesDefaultPort() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com:80' => 'http://www.example.com/'
        ));
        
        $this->runInputToExpectedOutputTests();
    } 
    
    public function testNormlisedUrlDoesNotRemoveNonDefaultPort() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com:443' => 'http://www.example.com:443/'
        ));
        
        $this->runInputToExpectedOutputTests();
    }     
}