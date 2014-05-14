<?php

    /**
     * Remote object representation
     *
     * @package known
     * @subpackage core
     */

    namespace known\Entities {

        class RemoteObject extends \known\Entities\Object implements \JsonSerializable
        {

            public function save()
            {
                // TODO: use a remote API to save to external sources if we have permission to
                return false;
            }

        }

    }