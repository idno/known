<?php

namespace Tests\API {

    /**
     * Initial api tests.
     */
    class BasicAPITest extends \Tests\KnownTestCase
    {

        /**
         * Test opening an API connection
         */
        public function testConnection()
        {

            $user = \Tests\KnownTestCase::user();
            $endpoint = \Idno\Core\Idno::site()->config()->getDisplayURL() . '';

            $result = \Idno\Core\Webservice::get(
                $endpoint, [], [
                'Accept: application/json',
                ]
            );

            $content = json_decode($result['content']);
            $response = $result['response'];

            $this->assertEmpty($result['error'], 'The result\'s error property should be empty.');
            $this->assertNotEmpty($content, 'Retrieved content should not be empty. Have you set the KNOWN_DOMAIN environment variable? Endpoint: ' . $endpoint);
            $this->assertEquals($response, 200, 'The response should have returned a 200 HTTP response.');

        }

        /**
         * Test that making a test post works while authenticated
         */
        public function testAuthenticatedPost()
        {

            $user = \Tests\KnownTestCase::user();
            $endpoint = \Idno\Core\Idno::site()->config()->url . 'status/edit';

            $result = \Idno\Core\Webservice::post(
                $endpoint, [
                'body' => "Making a test post via the API",
                ], [
                'Accept: application/json',
                'X-KNOWN-USERNAME: ' . $user->handle,
                'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/status/edit', $user->getAPIkey(), true)),
                ]
            );

            $content = json_decode($result['content']);
            $response = $result['response'];

            $this->assertEmpty($result['error'], 'The result\'s error property should be empty.');
            $this->assertNotEmpty($content, 'Retrieved content should not be empty. Have you set the KNOWN_DOMAIN environment variable? Endpoint: ' . $endpoint);
            $this->assertNotEmpty($content->location, 'Response should contain the location of the post.');
            $this->assertEquals($response, 200, 'The response should have returned a 200 HTTP response.');

        }
    }

}
