<?php

namespace Tests {
    
    /**
     * Test basic crypto functions.
     */
    class CryptoTest extends KnownTestCase {
        
        public function testOpenSSLExists() {
            $this->assertTrue(function_exists('openssl_random_pseudo_bytes'));
        }
        
        public function testStrong() {
            $bytes = openssl_random_pseudo_bytes(32, $cstrong);
            
            $this->assertTrue($cstrong);
        }
    }
}