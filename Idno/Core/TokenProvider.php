<?php

    namespace Idno\Core {

        use Idno\Common\Component;

        class TokenProvider extends Component
        {

            function generateToken($length)
            {

                return openssl_random_pseudo_bytes($length);

            }

        }

    }