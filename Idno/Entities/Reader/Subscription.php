<?php

    namespace Idno\Entities\Reader {

        use Idno\Common\Entity;

        class Subscription extends Entity
        {

            public $collection = 'reader';
            public static $retrieve_collection = 'reader';

            /**
             * Sets the URL of the feed this subscription belongs to
             * @param $url
             */
            function setFeedURL($url)
            {
                $this->feed_url = $url;
            }

            /**
             * Retrieves the URL of the feed this subscription belongs to
             * @param $url
             * @return mixed
             */
            function getFeedURL()
            {
                return $this->feed_url;
            }

            /**
             * Returns the feed associated with this subscription
             * @return bool|false|Entity|Feed
             */
            function getFeedObject()
            {
                if ($feed_url = $this->getFeedURL()) {
                    return Feed::getOne(array('feed_url' => $feed_url));
                }

                return false;
            }

            /**
             * Get a user's subscriptions
             * @param $user
             * @return array
             */
            static function getByUser($user)
            {
                return Subscription::get(array('owner' => $user->getUUID()));
            }

        }

    }