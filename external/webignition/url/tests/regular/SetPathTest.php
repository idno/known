<?php

/**
 * Check that URL properties can be set
 *  
 */
class SetPathTest extends AbstractRegularUrlTest {  
    
    public function testSetPlainPath() {
        $url = new \webignition\Url\Url('http://example.com/');
                
        $url->setPath('/path');        
        $this->assertEquals('/path', $url->getPath());
        $this->assertEquals('http://example.com/path', (string)$url);
    } 
    
    public function testSetPathWhenExistingPathIsUrlEncoded() {
        $url = new \webignition\Url\Url('js/scriptaculous.js?load=effects,builder');
        $this->assertEquals('js/scriptaculous.js?load=effects%2Cbuilder', (string)$url);
        
        $url->setPath('/js/scriptaculous.js');
        $this->assertEquals('/js/scriptaculous.js?load=effects%2Cbuilder', (string)$url);
    }    
}