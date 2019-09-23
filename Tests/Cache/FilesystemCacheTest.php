<?php

class FilesystemCacheTest extends \Tests\KnownTestCase
{

    function testFilesystemCache() {
        
        
        $cache = new \Idno\Caching\FilesystemCache();
        
        $this->assertEmpty($cache->load('test'));
        
        $this->assertTrue($cache->store('test', 12345));
        
        $this->assertEquals($cache->load('test'), 12345);
    }
    
}