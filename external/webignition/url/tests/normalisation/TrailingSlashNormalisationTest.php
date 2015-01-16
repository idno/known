<?php

/**
 * Check that normalisation appends a trailing slash to directory-ending URLs
 * http://www.example.com => http://www.example.com/
 *   
 */
class TrailingSlashNormalisationTest extends AbstractNormalisedUrlTest {   

    public function testNormalisedUrlAddsLeadingSlash() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com' => 'http://www.example.com/'
        ));
        
        $this->runInputToExpectedOutputTests();
    }     
    
    public function testNormalisedUrlDoesNotAllTrailingSlash() {      
        $this->setInputAndExpectedOutputUrls(array(
            'http://www.example.com/part1' => 'http://www.example.com/part1',
            'http://www.example.com/part1/' => 'http://www.example.com/part1/',
            'http://www.example.com/part1/part2' => 'http://www.example.com/part1/part2',
            'http://www.example.com/part1/part2/' => 'http://www.example.com/part1/part2/',
            'http://www.example.com/part1/part2/example.html' => 'http://www.example.com/part1/part2/example.html'
        ));
        
        $this->runInputToExpectedOutputTests();
    } 
}