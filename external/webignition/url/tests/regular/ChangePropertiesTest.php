<?php

/**
 * Check that URL properties can be changed
 *  
 */
class ChangePropertiesTest extends AbstractRegularUrlTest {      
    
    public function testChangeScheme() {        
        $url = new \webignition\Url\Url('http://example.com/');        
                
        $url->setScheme('https');        
        $this->assertEquals('https', $url->getScheme());
        $this->assertEquals('https://example.com/', (string)$url);
    }
    
    public function testChangeUserWithOnlyUser() {        
        $url = new \webignition\Url\Url('http://user:@example.com/');        
                
        $url->setUser('new-user');        
        $this->assertEquals('new-user', $url->getUser());
        $this->assertEquals('http://new-user:@example.com/', (string)$url);
    }     
    
    public function testChangeUserWithPassword() {        
        $url = new \webignition\Url\Url('http://user:pass@example.com/');        
                
        $url->setUser('new-user');        
        $this->assertEquals('new-user', $url->getUser());
        $this->assertEquals('http://new-user:pass@example.com/', (string)$url);
    }    
    
    public function testChangePassWithOnlyPassword() {        
        $url = new \webignition\Url\Url('http://:pass@example.com/');        
                
        $url->setPass('new-pass');        
        $this->assertEquals('new-pass', $url->getPass());
        $this->assertEquals('http://:new-pass@example.com/', (string)$url);
    } 
    
    public function testChangePassWithExistingUser() {        
        $url = new \webignition\Url\Url('http://user:pass@example.com/');        
                
        $url->setPass('new-pass');        
        $this->assertEquals('new-pass', $url->getPass());
        $this->assertEquals('http://user:new-pass@example.com/', (string)$url);
    }
    
    public function testChangeHost() {        
        $url = new \webignition\Url\Url('http://example.com/');
                
        $url->setHost('new.example.com');        
        $this->assertEquals('new.example.com', $url->getHost());
        $this->assertEquals('http://new.example.com/', (string)$url);
    }
    
    public function testChangePath() {
        $url = new \webignition\Url\Url('http://example.com/path');
                
        $url->setPath('/new-path');        
        $this->assertEquals('/new-path', $url->getPath());
        $this->assertEquals('http://example.com/new-path', (string)$url);        
    }    
    
    public function testChangeQueryWithNoFragmentWithoutQuestionMark() {        
        $url = new \webignition\Url\Url('http://example.com/?key1=value1');                        
        
        $url->setQuery('key1=value2');
        $this->assertEquals('key1=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value2', (string)$url);      
    }
    
    public function testChangeQueryWithNoFragmentWithQuestionMark() {                
        $url = new \webignition\Url\Url('http://example.com/?key1=value1');                        

        $url->setQuery('?key1=value2');
        $this->assertEquals('key1=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value2', (string)$url);        
    }    
    
    
    public function testChangeQueryWithFragmentWithoutQuestionMark() {
        $url = new \webignition\Url\Url('http://example.com/?key1=value1#fragment');                        
        
        $url->setQuery('key1=value2');
        $this->assertEquals('key1=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value2#fragment', (string)$url);        
    }    
        
    public function testChangeQueryWithFragmentWithQuestionMark() {
        $url = new \webignition\Url\Url('http://example.com/?key1=value1#fragment');                        
        
        $url->setQuery('?key1=value2');
        $this->assertEquals('key1=value2', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value2#fragment', (string)$url);        
    }    
    
    public function testChangeFragmentWithoutHash() {
        $url = new \webignition\Url\Url('http://example.com/#fragment');                        
        
        $url->setFragment('new-fragment');
        $this->assertEquals('new-fragment', $url->getFragment());
        $this->assertEquals('http://example.com/#new-fragment', (string)$url);         
    }
    
    public function testChangeFragmentWithHash() {
        $url = new \webignition\Url\Url('http://example.com/#fragment');                        
        
        $url->setFragment('#new-fragment');
        $this->assertEquals('new-fragment', $url->getFragment());
        $this->assertEquals('http://example.com/#new-fragment', (string)$url);        
    }    
}