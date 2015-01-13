<?php

/**
 * Check that URL properties can be set
 *  
 */
class SetNormalisedPropertiesTest extends AbstractNormalisedUrlTest {   
    
    public function testSetScheme() {         
        $url = new \webignition\NormalisedUrl\NormalisedUrl($this->urls['protocol-relative']);                        
        $url->setScheme('hTTp');
        
        $this->assertEquals('http', $url->getScheme());        
        $this->assertEquals($this->urls['complete'], (string)$url);
    }
    
    public function testSetSchemeForUrlThatHasNoHost() {         
        $url = new \webignition\NormalisedUrl\NormalisedUrl($this->urls['root-relative']);                        
        $url->setScheme('hTTp');
        
        $this->assertNull($url->getScheme());
     }  
   
    public function testSetHost() {        
        $url = new \webignition\NormalisedUrl\NormalisedUrl($this->urls['root-relative']);
                
        $url->setHost('eXAMPle.com');        
        $this->assertEquals('example.com', $url->getHost());
        $this->assertEquals('//example.com' . $this->urls['root-relative'], (string)$url);
    }    
    
    public function testSetPath() {
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');
                
        $url->setPath('/path/././././././././');        
        $this->assertEquals('/path/', $url->getPath());
        $this->assertEquals('http://example.com/path/', (string)$url);
    } 
    
    public function testSetQueryWithNoFragmentWithoutQuestionMark() {        
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');                        
        
        $url->setQuery('key2=value2&key1=value1');
        $this->assertEquals('key1=value1&key2=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1&key2=value2', (string)$url);      
    }
    
    public function testSetQueryWithNoFragmentWithQuestionMark() {                
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/');                        

        $url->setQuery('?key2=value2&key1=value1');
        $this->assertEquals('key1=value1&key2=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1&key2=value2', (string)$url);        
    }    
    
    public function testSetQueryWithFragmentWithoutQuestionMark() {
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/#fragment');                        
        
        $url->setQuery('key2=value2&key1=value1');
        $this->assertEquals('key1=value1&key2=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1&key2=value2#fragment', (string)$url);        
    }    
    
    public function testSetQueryWithFragmentWithQuestionMark() {
        $url = new \webignition\NormalisedUrl\NormalisedUrl('http://example.com/#fragment');                        
        
        $url->setQuery('?key2=value2&key1=value1');
        $this->assertEquals('key1=value1&key2=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1&key2=value2#fragment', (string)$url);        
    } 
}