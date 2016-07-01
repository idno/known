<?php

    /**
     * Notification representation
     */

    namespace Idno\Entities {

        use Idno\Common\Entity;
        use Idno\Core\Idno;

        class Notification extends \Idno\Common\Entity
        {

            /**
             * Set up a unique key for this notification so we can
             * avoid sending repeat notifications for the same
             * thing. e.g., for a webmention, [$source, $target].
             *
             * This affects the result of getURL()
             *
             * @param array $params
             * @return true if the notification key represents a
             * unique notification, false if we've seen this one
             * before.
             */
            function setNotificationKey(array $params)
            {
                $this->notificationKey = md5(implode('|', $params));
                $preexisting = self::getOne(['notificationKey' => $this->notificationKey]);
                return $preexisting === false;
            }

            /**
             * The short text message to notify the user with. (eg, a
             * subject line.)
             * @param string $message
             */
            function setMessage($message)
            {
                $this->message = $message;
                $this->setTitle($message);
            }

            function getMessage()
            {
                return $this->message;
            }

            /**
             * A template name pointing to a longer version of the
             * message with more detail.
             * @param string $template
             *
             */
            function setMessageTemplate($template)
            {
                $this->messageTemplate = $template;
            }

            function getMessageTemplate()
            {
                return $this->messageTemplate;
            }

            /**
             * @param string $actor URL (or UUID if local) of the
             * person who initiated the action
             */
            function setActor($actor)
            {
                if ($actor instanceof User) {
                    $this->actor = $actor->getUUID();
                } else {
                    $this->actor = $actor;
                }
            }

            function getActor()
            {
                if (is_string($this->actor)) {
                    return User::getByUUID($this->actor);
                }

                return $this->actor;
            }

            /**
             * Optionally, a string describing the kind of action. eg,
             * "comment", "like", "share", or "follow".
             * @param string $verb
             */
            function setVerb($verb)
            {
                $this->verb = $verb;
            }

            /**
             * Optionally, an array describing the object of the
             * action. eg, if this is a comment, the object will be
             * the array that represents the annotation.
             * Note: unlike ActivityStreamsPost, object is not usually an Entity.
             * @param array|false $object
             */
            function setObject($object)
            {
                if ($object instanceof Entity) {
                    $this->object = $object->getUUID();
                } else {
                    $this->object = $object;
                }
            }

            function getObject()
            {
                if (is_string($this->object)) {
                    return Entity::getByUUID($this->object);
                }

                return $this->object;
            }

            /**
             * Optionally, the indirect object of the action. If this
             * is a reply, this is the post that it is in-reply-to.
             */
            function setTarget($target)
            {
                if ($target instanceof Entity) {
                    $this->target = $target->getUUID();
                } else {
                    $this->target = $target;
                }
            }

            /**
             * Retrieve the indirect object of the action
             * @return bool|Entity
             */
            function getTarget()
            {
                if (is_string($this->target)) {
                    return Entity::getByUUID($this->target);
                }

                return $this->target;
            }

            /**
             * Has this notification been read?
             * @return bool
             */
            function isRead()
            {
                return $this->read;
            }

            /**
             * Mark this notification as read
             */
            function markRead()
            {
                $this->read = true;
            }

            /**
             * Mark this notification as unread
             */
            function markUnread()
            {
                $this->read = false;
            }

            function getURL()
            {
                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }

                if (!empty($this->notificationKey)) {
                    return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'notification/' . $this->notificationKey;
                }

                return \Idno\Core\Idno::site()->config()->url . 'view/' . $this->getID();
            }

            function saveDataFromInput()
            {
                if ($page = \Idno\Core\Idno::site()->currentPage()) {
                    $read = $page->getInput("read");
                    if ($read === 'true') {
                        $this->markRead();
                    } else if ($read === 'false') {
                        $this->markUnread();
                    }

                    $this->save();
                }
            }

            /**
             * Count the number of unread notifications for the specified user
             *
             * @param bool $user Optionally, a user to check for; otherwise checks current user
             * @return int
             */
            static function countUnread($user = false)
            {
                if (!$user) $user = Idno::site()->session()->currentUser();
                if (!($user instanceof User)) return 0;

                return self::countFromX('Idno\Entities\Notification', [
                    'owner' => $user->getUUID(),
                    'read' => ['$not' => true]
                ]);
            }

        }

    }