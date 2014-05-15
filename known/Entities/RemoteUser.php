<?php

    /**
     * Remote user representation
     *
     * @package known
     * @subpackage core
     */

    namespace known\Entities {

        class RemoteUser extends \known\Entities\User implements \JsonSerializable
        {

            public function save()
            {
                // TODO: use a remote API to save to external sources if we have permission to
                return false;
            }

        }

    }