<?php

/**
 * Check that absolute URLs are identified as such
 *  
 */
class IdentifyAbsoluteUrlTest extends AbstractRegularUrlTest {   
    
    public function testAbsoluteUrlIsRecognisedAsAbsolute() {        
        $url = new \webignition\Url\Url('http://example.com/absolute/url');
        
        $this->assertTrue($url->isAbsolute());
    }
    
}