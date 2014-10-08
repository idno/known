<?php

    namespace Idno\Entities\Reader {

        use Idno\Common\Entity;

        class Subscription extends Entity {

            public $collection = 'reader';
            public static $retrieve_collection = 'reader';

            /**
             * Sets the URL of the feed this subscription belongs to
             * @param $url
             */
            function setFeedURL($url) {
                $this->feed_url = $url;
            }

            /**
             * Retrieves the URL of the feed this subscription belongs to
             * @param $url
             * @return mixed
             */
            function getFeedURL($url) {
                return $this->feed_url;
            }

        }

    }