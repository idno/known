<?php

class FilesystemCacheTest extends \Tests\KnownTestCase
{

    /**
     * Test that the cache can store and receive data successfully
     */
    function testCanStoreAndRetrieveCacheData()
    {
        $cache = new \Idno\Caching\FilesystemCache();

        $name = 'test-' . substr(md5(rand()), 0, 10);
        $this->assertEmpty($cache->load($name), 'An initial value should not have been present for the specified key.');
        $this->assertTrue($cache->store($name, 12345), 'A value should have successfully stored at the specified key.');
        $this->assertEquals($cache->load($name), 12345, 'Once a value has been stored, that value should have been successfully retrieved at the specified key.');
    }

}
