<?php

    namespace Idno\Core {

        use Idno\Common\Component;

        class TokenProvider extends Component
        {

            function generateToken($length)
            {
                $strength = true;
                $bytes    = openssl_random_pseudo_bytes($length, $strength);

                if (!$strength) {
                    throw new \Exception("Token was generated using an a cryptographically weak algorithm, this probably means your version of OpenSSL is broken or very old.");
                }

                return $bytes;
            }

        }

    }