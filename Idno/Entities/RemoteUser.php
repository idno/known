<?php

    /**
     * Remote user representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        class RemoteUser extends \Idno\Entities\User implements \JsonSerializable
        {

            public function save($add_to_feed = false, $feed_verb = 'post')
            {
                // TODO: use a remote API to save to external sources if we have permission to
                // return false;

                // BUT for now, we still need to save some stub information in case we've just followed them
                return parent::save($add_to_feed, $feed_verb);
            }

            public function checkPassword($password)
            {
                return false; // Remote users can never log in
            }

            public function getURL()
            {

                // Remote users don't have a local profile, so we need to override the remote url
                if (!empty($this->url))
                    return $this->url;

                return $this->getUUID();
            }

            public function getUUID()
            {
                // Ensure UUID returns a local UUID as reference, so we can manage following etc
                if (!empty($this->uuid)) {
                    return $this->uuid;
                }
                if (!empty($this->_id)) {
                    return \Idno\Core\Idno::site()->config()->url . 'view/' . $this->_id;
                }
            }

            /**
             * Set this user's remote profile url.
             * @param type $url
             */
            public function setUrl($url)
            {
                $this->url = $url;
            }
        }

    }