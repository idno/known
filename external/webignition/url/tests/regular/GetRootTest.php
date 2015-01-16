<?php

/**
 * Check that a URL's root can be derived as expected
 *  
 */
class GetRootTest extends AbstractRegularUrlTest {      
    
    public function testRootUrlIsConstantWithRespectToPath() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $this->assertEquals('http://example.com', $url->getRoot());
        
        $url = new \webignition\Url\Url('http://example.com/index.html');
        $this->assertEquals('http://example.com', $url->getRoot());
        
        $url = new \webignition\Url\Url('http://example.com/path/to/application.php');
        $this->assertEquals('http://example.com', $url->getRoot());
    }   
}