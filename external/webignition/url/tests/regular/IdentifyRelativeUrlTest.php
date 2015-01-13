<?php

/**
 * Check that relative URLs are identified as such
 *  
 */
class IdentifyRelativeUrlTest extends AbstractRegularUrlTest {   
    
    public function testRelativeUrlIsRecognisedAsRelative() {        
        $url = new \webignition\Url\Url('/relative/url/here');
        
        $this->assertTrue($url->isRelative());
    }
    
    public function testAbsoluteUrlIsRecognisedAsNonRelative() {
        $url = new \webignition\Url\Url('http://example.com/absolute/url');
        
        $this->assertFalse($url->isRelative());        
    }
    
}