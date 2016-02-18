<?php

namespace Tests\Core {
    
    /**
     * Test gatekeepers
     */
    class GatekeeperTest extends \Tests\KnownTestCase  {
        
        public function testGatekeeper() {
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'account/settings/', [], []);
            
            $response = $result['response'];
            $this->assertTrue(empty($result['error']));
            $this->assertTrue($response == 403);
            
            $user = \Tests\KnownTestCase::user();
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->logUserOn($user)));
            
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'account/settings/', [], [
                'X-KNOWN-USERNAME: ' . $user->handle,
		'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/account/settings/', $user->getAPIkey(), true)),

            ]);
            
            $response = $result['response']; 
            $this->assertTrue(empty($result['error']));
            $this->assertTrue($response == 200);
            
            \Idno\Core\Idno::site()->session()->logUserOff();
        }
        
        public function testAdminGatekeeper() {
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'admin/', [], []);
            
            $response = $result['response'];
            $this->assertTrue(empty($result['error']));
            $this->assertTrue($response == 403);
            
            $user = \Tests\KnownTestCase::user();
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->logUserOn($user)));
            
            // Try normal user
            \Idno\Core\Idno::site()->session()->logUserOff();
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'admin/', [], [
                'X-KNOWN-USERNAME: ' . $user->handle,
		'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/admin/', $user->getAPIkey(), true)),

            ]);
            
            $response = $result['response'];
            $this->assertTrue(empty($result['error']));
            $this->assertTrue($response == 403);
            
            
            // Try admin 
            $user = \Tests\KnownTestCase::admin();
            $this->assertTrue(is_object(\Idno\Core\Idno::site()->session()->logUserOn($user)));
            
            $result = \Idno\Core\Webservice::get(\Idno\Core\Idno::site()->config()->url . 'admin/', [], [
                'X-KNOWN-USERNAME: ' . $user->handle,
		'X-KNOWN-SIGNATURE: ' . base64_encode(hash_hmac('sha256', '/admin/', $user->getAPIkey(), true)),

            ]);
                        
            $response = $result['response'];
            $this->assertTrue(empty($result['error']));
            $this->assertTrue($response == 403); // Admins can't be admins, so we expect a 403
            
            \Idno\Core\Idno::site()->session()->logUserOff();
        }
        
        
    }
    
}