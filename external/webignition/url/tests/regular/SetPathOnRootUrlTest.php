<?php

/**
 * Check that a path can be added to a bare root url
 *  
 */
class SetPathOnRootUrlTest extends AbstractRegularUrlTest {      
    
    public function testAddPathToRootUrl() {        
        $url = new \webignition\Url\Url('http://example.com');
        $url->setPath('/index.html');
        
        $this->assertEquals('http://example.com/index.html', (string)$url);        
        
        $url = new \webignition\Url\Url('http://example.com/');
        $url->setPath('/index.html');
        
        $this->assertEquals('http://example.com/index.html', (string)$url);           
    }
}