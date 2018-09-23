<?php

namespace Idno\Core {

    use Idno\Common\Component;

    class TokenProvider extends Component
    {

        /**
         * Generate a cryptographically secure random token, returning it as a HEX encoded string.
         * Note: Hex is two chars per byte, so $length = 16 would produce a 32 char string (same length as md5(rand()), but more secure)
         * @param int $length Length in bytes
         */
        function generateHexToken($length)
        {
            return bin2hex($this->generateToken($length));
        }

        /**
         * Generate a cryptographically secure random token.
         * @param type $length Length in bytes
         * @return bytes
         * @throws \Exception If cryptographic functions are not strong enough.
         */
        function generateToken($length)
        {
            $strength = true;
            $bytes    = openssl_random_pseudo_bytes($length, $strength);

            if (!$strength) {
                throw new \Idno\Exceptions\ConfigurationException(\Idno\Core\Idno::site()->language()->_("Token was generated using an a cryptographically weak algorithm, this probably means your version of OpenSSL is broken or very old."));
            }

            return $bytes;
        }

        /**
         * Helper that will return a partially redacted token for output.
         * Sometimes it is necessary to output a token, but you might not want
         * to output the whole thing since all you really want to know is if they're similar to another.
         * @param string $token
         * @return retacted token
         */
        public static function truncateToken($token)
        {
            return substr($token, 0, 3) . '[...]' . substr($token, -5);
        }

    }

}

