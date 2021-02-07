<?php

class FilesystemCacheTest extends \Tests\KnownTestCase
{

    function testFilesystemCache() {
        
        
        $cache = new \Idno\Caching\FilesystemCache();
        
        $name = 'test-' . substr(md5(rand()), 0, 10);

        $this->assertEmpty($cache->load($name));
        
        $this->assertTrue($cache->store($name, 12345));
        
        $this->assertEquals($cache->load($name), 12345);
    }
    
}