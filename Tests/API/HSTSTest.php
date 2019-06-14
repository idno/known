<?php

namespace Tests\API {

    /**
     * Test HSTS handling on webservice calls
     */
    class HSTSTest extends \Tests\KnownTestCase
    {

        function setUp(): void {
            
            // Tidy up
            if ($cache = \Idno\Core\Idno::site()->cache())
            {
                 $cache->delete(parse_url('localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('mapkyca.com', PHP_URL_HOST));
            }

        }
        
        function testNoHSTS() {
            
            $result = \Idno\Core\Webservice::get('http://localhost');

            $this->assertFalse(\Idno\Core\Webservice::isHSTS('http://localhost'));
            
        }
        
        function testHSTS()
        {
            // Call HTTPS endpoint (twice, first will fail)
            $result = \Idno\Core\Webservice::get('http://mapkyca.com');
            $result = \Idno\Core\Webservice::get('http://mapkyca.com');

            // Check storage
            $this->assertTrue(\Idno\Core\Webservice::isHSTS('http://mapkyca.com'));

        }
        
        function tearDown(): void {
            
            if ($cache = \Idno\Core\Idno::site()->cache())
            {
                 $cache->delete(parse_url('localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('mapkyca.com', PHP_URL_HOST));
            }
        }

    }

}