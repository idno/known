<?php

/**
 * Check that normalisation ignores host case
 *  
 */
class HostNormalisationTest extends AbstractNormalisedUrlTest {   
    
    public function testNormalisedUrlIgnoresHostCase() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.exaMPle.coM' => 'http://www.example.com/',           
            'http://www.example.com' => 'http://www.example.com/',
            'http://WWW.example.COM' => 'http://www.example.com/',
            'http://www.examplE.com' => 'http://www.example.com/'
        ));
        
        $this->runInputToExpectedOutputTests();
    } 
}