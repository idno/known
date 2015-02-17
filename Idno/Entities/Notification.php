<?php

    /**
     * Notification representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        class Notification extends \Idno\Entities\Object
        {

            public $subject;
            public $actor;
            public $object;
            public $url;

            function getTitle()
            {
                return '';
            }

            function getDescription()
            {
                return '';
            }

            /**
             * Set this notification as read
             */
            function setRead()
            {
                $this->read = 1;
            }

            /**
             * Mark this notification as unread
             */
            function setUnread()
            {
                $this->read = 0;
            }

            /**
             * Sets the body text of this notification
             * @param string $body
             */
            function setBody($body)
            {
                $this->body = $body;
            }

            /**
             * Has this notification been read?
             * @return bool
             */
            function isRead()
            {
                if (!empty($this->read)) {
                    return true;
                }

                return false;
            }

            function save($add_to_feed = false, $feed_verb = 'post')
            {
                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                if ($new) {
                    // TODO: email notification
                }

                return parent::save($add_to_feed, $feed_verb);
            }

        }

    }