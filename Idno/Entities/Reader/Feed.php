<?php

    namespace Idno\Entities\Reader {

        use Idno\Common\Entity;

        class Feed extends Entity {

            public $collection = 'reader';

            /**
             * Sets the URL of this feed
             * @param $url
             */
            function setURL($url) {
                $this->url = $url;
            }

            /**
             * Sets the time that this item was last updated
             * @param $time
             */
            function setLastUpdated($time) {
                $this->updated = (int) $time;
            }

        }

    }