<?php

/**
 * Check that a path can be added to a bare root url
 *  
 */
class SetPathOnNormalisedRootUrlTest extends AbstractNormalisedUrlTest {      
    
    public function testAddPathToRootUrl() {        
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com');
        $url->setPath('/index.html');
        
        $this->assertEquals('http://example.com/index.html', (string)$url);     
        
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
        $url->setPath('/index.html');
        
        $this->assertEquals('http://example.com/index.html', (string)$url);          
    }
}