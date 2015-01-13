<?php

/**
 * Check that a URL's root can be derived as expected
 *  
 */
class PathHasFilenameTest extends AbstractRegularUrlTest {      
    
    public function testDirectoryPathDoesNotHaveFilename() {        
        $path = new \webignition\Url\Path\Path('/example/');
        $this->assertFalse($path->hasFilename());
    }   
    
    public function testPathWithFilenameHasFilename() {        
        $path = new \webignition\Url\Path\Path('/example/path.txt');
        $this->assertTrue($path->hasFilename());
    }       
    
    public function testDirectoryPathThatLooksLikeFilenameDoesNotHaveFilename() {        
        $path = new \webignition\Url\Path\Path('/example/path.txt/');
        $this->assertFalse($path->hasFilename());
    }     
}