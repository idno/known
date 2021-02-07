<?php

namespace Tests\Core {

    /**
     * Test gatekeepers
     */
    class GatekeeperTest extends \Tests\KnownTestCase
    {

        public function testGatekeeper()
        {
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'account/settings/', [], []);

            $response = $result['response'];
            $this->assertTrue(empty($result['error']), 'The result should not contain an error property.');
            $this->assertTrue($response == 403, 'The response should have returned a 403 HTTP response.');

            $user = \Tests\KnownTestCase::user();
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->logUserOn($user)));

            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'account/settings/', [], [
                'X-KNOWN-USERNAME: ' . $user->handle,
                'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/account/settings/', $user->getAPIkey(), true)),

            ]);

            $response = $result['response'];
            $this->assertTrue(empty($result['error']), 'The result should not contain an error property.');
            $this->assertTrue($response == 200, 'The response should have returned a 200 HTTP response.');

            \Idno\Core\Idno::site()->session()->logUserOff();
        }

        public function testAdminGatekeeper()
        {
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'admin/', [], []);

            $response = $result['response'];
            $this->assertTrue(empty($result['error']), 'The result should not contain an error property.');
            $this->assertTrue($response == 403, 'The response should have returned a 403 HTTP response.');

            $user = \Tests\KnownTestCase::user();
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->logUserOn($user)));

            // Try normal user
            \Idno\Core\Idno::site()->session()->logUserOff();
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'admin/', [], [
                'X-KNOWN-USERNAME: ' . $user->handle,
                'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/admin/', $user->getAPIkey(), true)),

            ]);

            $response = $result['response'];
            $this->assertTrue(empty($result['error']), 'The result should not contain an error property.');
            $this->assertTrue($response == 403, 'The response should have returned a 403 HTTP response.');

            // Try admin
            $user = \Tests\KnownTestCase::admin();
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->logUserOn($user)));

            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'admin/', [], [
                'X-KNOWN-USERNAME: ' . $user->handle,
                'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/admin/', $user->getAPIkey(), true)),

            ]);

            $response = $result['response'];
            $this->assertTrue(empty($result['error']), 'The result should not contain an error property.');
            $this->assertTrue($response == 403, 'The response should have returned a 403 HTTP response.');

            \Idno\Core\Idno::site()->session()->logUserOff();
        }


    }

}
