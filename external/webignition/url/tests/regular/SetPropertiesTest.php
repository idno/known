<?php

/**
 * Check that URL properties can be set
 *  
 */
class SetPropertiesTest extends AbstractRegularUrlTest {   
    
    public function testSetScheme() {         
        $url = new \webignition\Url\Url($this->urls['protocol-relative']);                        
        $url->setScheme('http');
        
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals($this->urls['complete'], (string)$url);       
    }
    
    public function testSetSchemeForUrlThatHasNoHost() {         
        $url = new \webignition\Url\Url($this->urls['root-relative']);                        
        $url->setScheme('http');
        
        $this->assertNull($url->getScheme());
     }  
    
    public function testSetUserWithNoCredentials() {        
        $url = new \webignition\Url\Url('http://example.com/');        
                
        $url->setUser('user');        
        $this->assertEquals('user', $url->getUser());
        $this->assertEquals('http://user:@example.com/', (string)$url);
    }     
    
    public function testSetUserWithExistingPassword() {        
        $url = new \webignition\Url\Url('http://:pass@example.com/');        
                
        $url->setUser('user');        
        $this->assertEquals('user', $url->getUser());
        $this->assertEquals('http://user:pass@example.com/', (string)$url);
    }    
    
    public function testSetPassWithNoCredentials() {        
        $url = new \webignition\Url\Url('http://example.com/');        
                
        $url->setPass('pass');        
        $this->assertEquals('pass', $url->getPass());
        $this->assertEquals('http://:pass@example.com/', (string)$url);
    } 
    
    public function testSetPassWithExistingUser() {        
        $url = new \webignition\Url\Url('http://user:@example.com/');        
                
        $url->setPass('pass');        
        $this->assertEquals('pass', $url->getPass());
        $this->assertEquals('http://user:pass@example.com/', (string)$url);
    }    
    
    public function testSetHost() {        
        $url = new \webignition\Url\Url($this->urls['root-relative']);
                
        $url->setHost('example.com');        
        $this->assertEquals('example.com', $url->getHost());
        $this->assertEquals('//example.com' . $this->urls['root-relative'], (string)$url);
    }    
    
    public function testSetPath() {
        $url = new \webignition\Url\Url('http://example.com/');
                
        $url->setPath('/path');        
        $this->assertEquals('/path', $url->getPath());
        $this->assertEquals('http://example.com/path', (string)$url);
    } 
    
    public function testSetQueryWithNoFragmentWithoutQuestionMark() {        
        $url = new \webignition\Url\Url('http://example.com/');                        
        
        $url->setQuery('key1=value1');
        $this->assertEquals('key1=value1', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1', (string)$url);      
    }
    
    public function testSetQueryWithNoFragmentWithQuestionMark() {                
        $url = new \webignition\Url\Url('http://example.com/');                        

        $url->setQuery('?key1=value1');
        $this->assertEquals('key1=value1', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1', (string)$url);        
    }    
    
    public function testSetQueryWithFragmentWithoutQuestionMark() {
        $url = new \webignition\Url\Url('http://example.com/#fragment');                        
        
        $url->setQuery('key1=value1');
        $this->assertEquals('key1=value1', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1#fragment', (string)$url);        
    }    
    
    public function testSetQueryWithFragmentWithQuestionMark() {
        $url = new \webignition\Url\Url('http://example.com/#fragment');                        
        
        $url->setQuery('?key1=value1');
        $this->assertEquals('key1=value1', (string)$url->getQuery());
        $this->assertEquals('http://example.com/?key1=value1#fragment', (string)$url);        
    }
    
    public function testSetFragmentWithoutHash() {
        $url = new \webignition\Url\Url('http://example.com/');                        
        
        $url->setFragment('fragment');
        $this->assertEquals('fragment', $url->getFragment());
        $this->assertEquals('http://example.com/#fragment', (string)$url);         
    }
    
    public function testSetFragmentWithHash() {
        $url = new \webignition\Url\Url('http://example.com/');                        
        
        $url->setFragment('#fragment');
        $this->assertEquals('fragment', $url->getFragment());
        $this->assertEquals('http://example.com/#fragment', (string)$url);         
    }
    
    public function testSetHostForRelativeUrlWithPathOnly() {
        $url = new \webignition\Url\Url('path');
        
        $url->setHost('www.example.com');
        $this->assertEquals('www.example.com', $url->getHost());
        $this->assertEquals('//www.example.com/path', (string)$url);
    }
    
    public function testSetPathThenHostThenSchemeThenUser() {
        $url = new \webignition\Url\Url('example.php');
        
        $this->assertEquals('example.php', (string)$url);
        
        $url->setPath('/pathOne/Path2/path2/example2.php');        
        $this->assertEquals('/pathOne/Path2/path2/example2.php', (string)$url);
        
        $url->setHost('www.example.com');
        $this->assertEquals('//www.example.com/pathOne/Path2/path2/example2.php', (string)$url);
        
        $url->setScheme('http');
        $this->assertEquals('http://www.example.com/pathOne/Path2/path2/example2.php', (string)$url);
        
        $url->setUser('user');
        $this->assertEquals('http://user:@www.example.com/pathOne/Path2/path2/example2.php', (string)$url);
    }
}