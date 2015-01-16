<?php

/**
 *  
 */
class PathFilenameAndDirectoryTest extends AbstractRegularUrlTest {   
    
    public function testWithFilename() {        
        $url = new \webignition\Url\Url('http://www.example.com/test.html');
        
        $this->assertTrue($url->getPath()->hasFilename());
        $this->assertEquals('/', $url->getPath()->getDirectory());
        $this->assertEquals('test.html', $url->getPath()->getFilename());
    } 
    
    public function testWithoutFilename() {        
        $url = new \webignition\Url\Url('http://www.example.com/path/is/here');
        
        $this->assertFalse($url->getPath()->hasFilename());
        $this->assertEquals('/path/is/here', $url->getPath()->getDirectory());
        $this->assertEquals('', $url->getPath()->getFilename());
    }     
}