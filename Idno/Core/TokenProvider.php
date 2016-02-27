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
                    throw new \Idno\Exceptions\ConfigurationException("Token was generated using an a cryptographically weak algorithm, this probably means your version of OpenSSL is broken or very old.");
                }

                return $bytes;
            }

        }

    }