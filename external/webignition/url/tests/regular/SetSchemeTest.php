<?php

/**
 *  
 */
class SetSchemeTest extends AbstractRegularUrlTest {   
    
    public function testSetSchemeOnSchemelessUrl() {         
        $url = new \webignition\Url\Url('example.com');                        
        $url->setScheme('http');
        
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('http://example.com', (string)$url);       
    }
    
    public function testSetSchemeOnProtocolRelativeUrl() {         
        $url = new \webignition\Url\Url('//example.com');                        
        $url->setScheme('http');
        
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('http://example.com', (string)$url);       
    }    
}