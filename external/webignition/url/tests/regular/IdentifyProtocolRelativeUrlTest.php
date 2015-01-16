<?php
/**
 * Check that protocol relative URLs are identified as such
 *  
 */
class IdentifyProtocolRelativeUrlTest extends AbstractRegularUrlTest {   
    
    public function testRelativeUrlIsRecognisedAsRelative() {        
        $url = new \webignition\Url\Url('//example.com/protocol/relative/url');
        
        $this->assertTrue($url->isProtocolRelative());
    }
    
    public function testAbsoluteUrlIsRecognisedAsNonRelative() {
        $url = new \webignition\Url\Url('http://example.com/absolute/url');
        
        $this->assertFalse($url->isProtocolRelative());        
    }
    
}