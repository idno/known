<?php

/**
 * Check that URL properties can be set
 *  
 */
class SetPropertiesForFragmentOnlyUrlTest extends AbstractRegularUrlTest {   
    
    private $fragment = '#startcontent';
    
    /**
     *
     * @param string $inputUrl
     * @return \webignition\Url\Url 
     */
    protected function newUrl($inputUrl = null) {
        return new \webignition\Url\Url($this->fragment);
    }    
    
    public function testInitialUrl() {         
        $url = $this->newUrl();
        
        $this->assertTrue($url->hasFragment());
        $this->assertEquals('startcontent', $url->getFragment());
    }
    
    public function testSetPath() {
        $url = $this->newUrl();
        
        $url->setPath($this->completePath());
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals($this->completePath(), $url->getPath());
    }
}