<?php

namespace Tests {

    /**
     * Test basic crypto functions.
     */
    class CryptoTest extends KnownTestCase
    {

        /**
         * SSL Functions exists.
         */
        public function testOpenSSLExists()
        {
            $this->assertTrue(function_exists('openssl_random_pseudo_bytes'), 'openssl_random_pseudo_bytes must exist. OpenSSL may not be installed.');
        }

        /**
         * OpenSSL isn't broken on this system
         */
        public function testStrong()
        {
            $bytes = openssl_random_pseudo_bytes(32, $cstrong);

            $this->assertTrue($cstrong, 'OpenSSL should return a series of random bytes.');
        }

        /**
         * Some platforms (Windows I'm looking at you) only return a 32 bit randmax.
         */
        public function testRandomEntropy()
        {
            $this->assertTrue(getrandmax() > 32767, 'We need to be able to generate more than a 32-bit random number.');
        }

        /**
         * Site secret initialised and reasonably long. (Note, we can't check entropy here)
         */
        public function testSiteSecret()
        {
            $this->assertTrue((strlen(\Idno\Core\Idno::site()->config()->site_secret)>=64), 'The site secret should have been generated and reasonably strong.');
        }

        /**
         * Bonita now requires sha256
         */
        public function testAssertSha256()
        {

            $this->assertTrue(in_array('sha256', hash_algos()), 'We need sha256 to be supported.');
        }

        /**
         * Some configurations seem to advertise algorithms which they don't support, this causes problems.
         */
        public function testAlgortihms()
        {

            foreach (hash_algos() as $algo) {
                $secret = "secret";

                $result = hash($algo, $secret);
                $this->assertTrue(!empty($result), $algo . ' should return a hashed result.');
            }
        }

    }
}
