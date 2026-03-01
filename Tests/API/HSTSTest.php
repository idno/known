<?php

namespace Tests\API {

    /**
     * Test HSTS handling on webservice calls using synthetic headers
     * so tests do not depend on external websites being reachable.
     */
    class HSTSTest extends \Tests\IdnoTestCase
    {

        function setUp(): void
        {

            // Tidy up
            if ($cache = \Idno\Core\Idno::site()->cache()) {
                 $cache->delete(parse_url('http://localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('http://example.com', PHP_URL_HOST));
            }

        }

        /**
         * Test that a URL without HSTS headers is not detected as HSTS
         */
        function testNoHSTS()
        {

            // Feed headers that do NOT contain Strict-Transport-Security
            \Idno\Core\Webservice::checkForHSTSHeader(
                'http://localhost',
                ["HTTP/1.1 200 OK", "Content-Type: text/html"]
            );

            $this->assertFalse(\Idno\Core\Webservice::isHSTS('http://localhost'), 'Should have detected that http://localhost does not have HSTS headers.');

        }

        /**
         * Test that HSTS headers are detected and cached correctly
         */
        function testHSTS()
        {
            // Feed a synthetic Strict-Transport-Security header
            \Idno\Core\Webservice::checkForHSTSHeader(
                'http://example.com',
                ["HTTP/1.1 200 OK", "Strict-Transport-Security: max-age=31536000; includeSubDomains"]
            );

            $this->assertTrue(\Idno\Core\Webservice::isHSTS('http://example.com'), 'Should have detected HSTS headers from the synthetic response.');

        }

        function tearDown(): void
        {

            if ($cache = \Idno\Core\Idno::site()->cache()) {
                 $cache->delete(parse_url('http://localhost', PHP_URL_HOST));
                 $cache->delete(parse_url('http://example.com', PHP_URL_HOST));
            }
        }

    }

}
