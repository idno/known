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

            public function save($add_to_feed = false, $feed_verb = 'post')
            {
                // TODO: use a remote API to save to external sources if we have permission to
                return false;
            }

        }

    }