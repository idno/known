<?php


namespace Tests\API {

    /**
     * Test file uploads (as this often gets broken)
     */
    class UploadTest extends \Tests\KnownTestCase
    {

        private static $file = 'photo.jpg';

        public function testUpload()
        {
            $user = \Tests\KnownTestCase::user();

            $result = \Idno\Core\Webservice::post(\Idno\Core\Idno::site()->config()->url . 'photo/edit', [
                'title' => 'A Photo upload',
                'body' => "Uploading a pretty picture via the api",
                'photo' => \Idno\Core\WebserviceFile::createFromCurlString("@" . dirname(__FILE__) . "/" . self::$file . ";filename=Photo.jpg;type=image/jpeg")
            ], [
                'Accept: application/json',
                'X-KNOWN-USERNAME: ' . $user->handle,
                'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/photo/edit', $user->getAPIkey(), true)),
            ]);

            print_r($result);
            $content = json_decode($result['content']);
            $response = $result['response'];

            $this->assertTrue(empty($result['error']));
            $this->assertTrue(!empty($content));
            $this->assertTrue(!empty($content->location));
            $this->assertTrue($response == 200);
        }
    }
}

