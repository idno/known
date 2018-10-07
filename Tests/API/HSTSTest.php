<?php

namespace Tests\API {

    /**
     * Test HSTS handling on webservice calls
     */
    class HSTSTest extends \Tests\KnownTestCase
    {

        function testHSTS()
        {

            // Tidy up
            if ($cache = \Idno\Core\Idno::site()->cache())
            {
                 $cache->delete(parse_url('localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('mapkyca.com', PHP_URL_HOST));
            }

            $result = \Idno\Core\Webservice::get('http://localhost');

            $this->assertFalse(\Idno\Core\Webservice::isHSTS('http://localhost'));

            // Call HTTPS endpoint
            $result = \Idno\Core\Webservice::get('http://mapkyca.com');
            $result = \Idno\Core\Webservice::get('http://mapkyca.com');

            // Check storage
            $this->assertTrue(\Idno\Core\Webservice::isHSTS('http://mapkyca.com'));

            // Tidy up so we can re-run test
            if ($cache = \Idno\Core\Idno::site()->cache())
            {
                 $cache->delete(parse_url('localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('mapkyca.com', PHP_URL_HOST));
            }

        }

    }

}