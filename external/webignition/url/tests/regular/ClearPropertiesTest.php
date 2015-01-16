<?php

/**
 *  
 */
class ClearPropertiesTest extends AbstractRegularUrlTest {      

    public function testAddNullFragment() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $url->setFragment(null);
        
        $this->assertNull($url->getFragment());
        $this->assertEquals("http://example.com/", (string)$url);
    }     
    
    public function testReplaceFragmentWithNull() {        
        $url = new \webignition\Url\Url('http://example.com/#bob');
        $url->setFragment(null);
        
        $this->assertNull($url->getFragment());
        $this->assertEquals("http://example.com/", (string)$url);
    }    
    
    public function testAddNullPass() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $url->setPass(null);
        
        $this->assertNull($url->getPass());
        $this->assertEquals("http://example.com/", (string)$url);
    }     
    
    public function testReplacePassWithNull() {        
        $url = new \webignition\Url\Url('http://:pass@example.com/');
        $url->setPass(null);
        
        $this->assertNull($url->getPass());
        $this->assertEquals("http://example.com/", (string)$url);
    }     
    
    public function testAddNullQuery() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $url->setQuery(null);
        
        $this->assertNull($url->getQuery());
        $this->assertEquals("http://example.com/", (string)$url);
    }     
    
    public function testReplaceQueryWithNull() {        
        $url = new \webignition\Url\Url('http://example.com/?param=value');
        $url->setQuery(null);
        
        $this->assertNull($url->getQuery());
        $this->assertEquals("http://example.com/", (string)$url);
    } 
    
    
    public function testAddNullUser() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $url->setUser(null);
        
        $this->assertNull($url->getUser());
        $this->assertEquals("http://example.com/", (string)$url);
    }     
    
    public function testReplaceUserWithNull() {        
        $url = new \webignition\Url\Url('http://user:@example.com/');
        $url->setUser(null);
        
        $this->assertNull($url->getUser());
        $this->assertEquals("http://example.com/", (string)$url);
    }        
    
    public function testAddNullPath() {        
        $url = new \webignition\Url\Url('http://example.com');
        $url->setPath(null);
        
        $this->assertNull($url->getPath());
        $this->assertEquals("http://example.com", (string)$url);
    }     
    
    public function testReplacePathWithNull() {        
        $url = new \webignition\Url\Url('http://example.com/path/here');
        $url->setPath(null);
        
        $this->assertNull($url->getPath());
        $this->assertEquals("http://example.com", (string)$url);
    }         
    
    public function testAddNullPort() {        
        $url = new \webignition\Url\Url('http://example.com/');
        $url->setPort(null);
        
        $this->assertNull($url->getPort());
        $this->assertEquals("http://example.com/", (string)$url);
    }     
    
    public function testReplacePortWithNull() {        
        $url = new \webignition\Url\Url('http://example.com:443/');
        $url->setPort(null);
        
        $this->assertNull($url->getPort());
        $this->assertEquals("http://example.com/", (string)$url);
    }     
    
    public function testAddNullFragmentPassPathPortQueryUser() {        
        $url = new \webignition\Url\Url('http://example.com');
        $url->setFragment(null);
        $url->setPass(null);
        $url->setPath(null);
        $url->setPort(null);
        $url->setQuery(null);
        $url->setUser(null);
        
        $this->assertNull($url->getFragment());
        $this->assertNull($url->getPass());
        $this->assertNull($url->getPath());
        $this->assertNull($url->getPort());
        $this->assertNull($url->getQuery());
        $this->assertNull($url->getUser());
        
        $this->assertEquals("http://example.com", (string)$url);
    } 
    
    
    public function testReplaceFragmentPassPathPortQueryUserWithNull() {        
        $url = new \webignition\Url\Url('http://user:pass@example.com:443/path/here?param=value#fragment');
        $url->setFragment(null);
        $url->setPass(null);
        $url->setPath(null);
        $url->setPort(null);
        $url->setQuery(null);
        $url->setUser(null);
        
        $this->assertNull($url->getFragment());
        $this->assertNull($url->getPass());
        $this->assertNull($url->getPath());
        $this->assertNull($url->getPort());
        $this->assertNull($url->getQuery());
        $this->assertNull($url->getUser());
        
        $this->assertEquals("http://example.com", (string)$url);
    } 
    
    
    /**
     * When setting a fragment to null in a url that had a fragment
     * and then setting the query to null where there was no query
     * was resulting in the fragment containing the string '?', this is incorrect
     */
    public function testReplaceFragmentWithNullSetNullQuery() {        
        $url = new \webignition\Url\Url('http://example.com/#fragment');
        $url->setFragment(null);
        $url->setQuery(null);
        
        $this->assertNull($url->getFragment());
        $this->assertNull($url->getQuery());
        
        $this->assertEquals("http://example.com/", (string)$url);
    }     
}