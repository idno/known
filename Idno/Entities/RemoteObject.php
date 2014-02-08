<?php

    /**
     * Remote object representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        class RemoteObject extends \Idno\Entities\Object implements \JsonSerializable
        {

            public function save()
            {
                // TODO: use a remote API to save to external sources if we have permission to
                return false;
            }

        }

    }