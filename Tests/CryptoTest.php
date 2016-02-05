<?php

namespace Tests {
    
    /**
     * Test basic crypto functions.
     */
    class CryptoTest extends KnownTestCase {
        
        /**
         * SSL Functions exists.
         */
        public function testOpenSSLExists() {
            $this->assertTrue(function_exists('openssl_random_pseudo_bytes'));
        }
        
        /**
         * OpenSSL isn't broken on this system
         */
        public function testStrong() {
            $bytes = openssl_random_pseudo_bytes(32, $cstrong);
            
            $this->assertTrue($cstrong);
        }
        
        /**
         * Site secret initialised and reasonably long. (Note, we can't check entropy here)
         */
        public function testSiteSecret() {
            $this->assertTrue((strlen(\Idno\Core\Idno::site()->config()->site_secret)>=64));
        }
    }
}