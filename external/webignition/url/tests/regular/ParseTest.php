<?php

/**
 * Check that URL can be correctly parsed out into its component parts
 *  
 */
class ParseTest extends AbstractRegularUrlTest {   
    
    public function testCompleteUrl() {        
        $url = new \webignition\Url\Url($this->urls['complete']);        
        
        $this->assertTrue($url->hasScheme());
        $this->assertEquals(self::SCHEME_HTTP, $url->getScheme());
        
        $this->assertTrue($url->hasHost());
        $this->assertEquals(self::HOST, $url->getHost());
        
        $this->assertTrue($url->hasPort());
        $this->assertEquals(self::PORT_REGULAR, $url->getPort());
        
        $this->assertTrue($url->hasUser());
        $this->assertEquals(self::USER, $url->getUser());
        
        $this->assertTrue($url->hasPass());
        $this->assertEquals(self::PASS, $url->getPass());
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals(self::PATH_PART_DELIMITER . $this->completePath(), $url->getPath());
        
        $this->assertTrue($url->hasQuery());
        $this->assertEquals($this->completeUrlQueryString(), $url->getQuery());
        
        $this->assertTrue($url->hasFragment());
        $this->assertEquals(self::FRAGMENT, $url->getFragment());
        
        $this->assertEquals($this->urls['complete'], (string)$url);
    }
    
    public function testProtocolRelativeUrl() {        
        $url = new \webignition\Url\Url($this->urls['protocol-relative']);        
        
        $this->assertFalse($url->hasScheme());        
        $this->assertNull($url->getScheme());
        
        $this->assertTrue($url->hasHost());
        $this->assertEquals(self::HOST, $url->getHost());
        
        $this->assertTrue($url->hasPort());
        $this->assertEquals(self::PORT_REGULAR, $url->getPort());
        
        $this->assertTrue($url->hasUser());
        $this->assertEquals(self::USER, $url->getUser());
        
        $this->assertTrue($url->hasPass());
        $this->assertEquals(self::PASS, $url->getPass());
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals(self::PATH_PART_DELIMITER . $this->completePath(), $url->getPath());
        
        $this->assertTrue($url->hasQuery());
        $this->assertEquals($this->completeUrlQueryString(), $url->getQuery());
        
        $this->assertTrue($url->hasFragment());
        $this->assertEquals(self::FRAGMENT, $url->getFragment());
        
        $this->assertEquals($this->urls['protocol-relative'], (string)$url);        
    }

    public function testRootRelativeUrl() {        
        $url = new \webignition\Url\Url($this->urls['root-relative']);
        
        $this->assertFalse($url->hasScheme());
        $this->assertNull($url->getScheme());
        
        $this->assertFalse($url->hasHost());
        $this->assertNull($url->getHost());
        
        $this->assertFalse($url->hasPort());
        $this->assertNull($url->getPort());
        
        $this->assertFalse($url->hasUser());
        $this->assertNull($url->getUser());
        
        $this->assertFalse($url->hasPass());
        $this->assertNull($url->getPass());
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals(self::PATH_PART_DELIMITER . $this->completePath(), $url->getPath());
        
        $this->assertTrue($url->hasQuery());
        $this->assertEquals($this->completeUrlQueryString(), $url->getQuery());
        
        $this->assertTrue($url->hasFragment());
        $this->assertEquals(self::FRAGMENT, $url->getFragment());
        
        $this->assertEquals($this->urls['root-relative'], (string)$url);        
    }    
    
    public function testRelativeUrl() {        
        $url = new \webignition\Url\Url($this->urls['relative']);
        
        $this->assertFalse($url->hasScheme());
        $this->assertNull($url->getScheme());
        
        $this->assertFalse($url->hasHost());
        $this->assertNull($url->getHost());
        
        $this->assertFalse($url->hasPort());
        $this->assertNull($url->getPort());
        
        $this->assertFalse($url->hasUser());
        $this->assertNull($url->getUser());
        
        $this->assertFalse($url->hasPass());
        $this->assertNull($url->getPass());
        
        $this->assertTrue($url->hasPath());
        $this->assertEquals($this->completePath(), $url->getPath());
        
        $this->assertTrue($url->hasQuery());
        $this->assertEquals($this->completeUrlQueryString(), $url->getQuery());
        
        $this->assertTrue($url->hasFragment());
        $this->assertEquals(self::FRAGMENT, $url->getFragment());
        
        $this->assertEquals($this->urls['relative'], (string)$url);        
    }
    
}