<?php


class PathHasTrailingSlashTest extends AbstractRegularUrlTest {      
    
    public function testDirectroyPathWithTrailingSlashHasTrailingSlash() {        
        $path = new \webignition\Url\Path\Path('/example/');
        $this->assertTrue($path->hasTrailingSlash());
    }   
    
    public function testDirectroyPathWithoutTrailingSlashHasNoTrailingSlash() {        
        $path = new \webignition\Url\Path\Path('/example');
        $this->assertFalse($path->hasTrailingSlash());
    }       
    
    public function testPathWithFilenameHasFilenameWithoutTrailingSlashHasNoTrailingSlash() {        
        $path = new \webignition\Url\Path\Path('/example/path.txt');
        $this->assertFalse($path->hasTrailingSlash());
    }       
    
    public function testDirectoryPathThatLooksLikeFilenameWithTrailingSlashHasTrailingSlash() {        
        $path = new \webignition\Url\Path\Path('/example/path.txt/');
        $this->assertTrue($path->hasTrailingSlash());
    }     
}