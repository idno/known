<?php


namespace Tests\API {

    /**
     * Test photo uploads
     */
    class UploadTest extends \Tests\KnownTestCase
    {

        private static $file = 'photo.jpg';

        public function testPhotoUpload()
        {
            $user = \Tests\KnownTestCase::user();
            $endpoint = \Idno\Core\Idno::site()->config()->url . 'photo/edit';

            $result = \Idno\Core\Webservice::post($endpoint, [
                'title' => 'A Photo upload',
                'body' => "Uploading a pretty picture via the api",
                'photo' => \Idno\Core\WebserviceFile::createFromCurlString("@" . dirname(__FILE__) . "/" . self::$file . ";filename=Photo.jpg;type=image/jpeg")
            ], [
                'Accept: application/json',
                'X-KNOWN-USERNAME: ' . $user->handle,
                'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/photo/edit', $user->getAPIkey(), true)),
            ]);

            $content = json_decode($result['content']);
            $response = $result['response'];

            $this->assertEmpty($result['error'], 'The result should not contain an error property.');
            $this->assertNotEmpty($content, 'Retrieved content should not be empty. Have you set the KNOWN_DOMAIN environment variable? Endpoint: ' . $endpoint);
            $this->assertNotEmpty($content->location, 'Response should contain the location of the post.');
            $this->assertEquals($response, 200,  'The response should have returned a 200 HTTP response.');
        }
    }
}

