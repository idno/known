<?php

namespace Tests\API {

    /**
     * Test HSTS handling on webservice calls
     *
     * @TODO: mock endpoints rather than having them call real sites; what if the user really does have HSTS headers on localhost?
     */
    class HSTSTest extends \Tests\KnownTestCase
    {

        function setUp(): void
        {

            // Tidy up
            if ($cache = \Idno\Core\Idno::site()->cache()) {
                 $cache->delete(parse_url('http://localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('http://mapkyca.com', PHP_URL_HOST));
            }

        }

        /**
         * Test that the specified endpoint doesn't have HSTS headers
         */
        function testNoHSTS()
        {

            $result = \Idno\Core\Webservice::get('http://localhost');

            $this->assertFalse(\Idno\Core\Webservice::isHSTS('http://localhost'), 'Should have detected that http://localhost does not have HSTS headers.');

        }

        /**
         * Test that the specified endpoint has HSTS headers
         *
         * @TODO: fix this so it doesn't depend on a particular website being online
         */
        function testHSTS()
        {
            // Call HTTPS endpoint (twice, first will fail)
            $result = \Idno\Core\Webservice::get('http://mapkyca.com');
            $result = \Idno\Core\Webservice::get('http://mapkyca.com');

            // Check storage
            $this->assertTrue(\Idno\Core\Webservice::isHSTS('http://mapkyca.com'), 'Should have detected that http://mapkyca.com has HSTS headers.');

        }

        function tearDown(): void
        {

            if ($cache = \Idno\Core\Idno::site()->cache()) {
                 $cache->delete(parse_url('http://localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('http://mapkyca.com', PHP_URL_HOST));
            }
        }

    }

}
