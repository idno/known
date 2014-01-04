<?php

    /**
     * User representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        // We need the PHP 5.5 password API
        require_once \Idno\Core\site()->config()->path . '/external/password_compat/lib/password.php';

        class User extends \Idno\Common\Entity implements \JsonSerializable
        {

            /**
             * Overloading the constructor for users to make it explicit that
             * they don't have owners
             */

            function __construct()
            {

                parent::__construct();
                $this->owner = false;

                // Hook to add user data to webfinger
                \Idno\Core\site()->addEventHook('webfinger', function (\Idno\Core\Event $event) {

                    $user = $event->data()['object'];

                    $links = $event->response();
                    if (empty($links)) $links = array();

                    if ($user instanceof User) {
                        $links = array(
                            array(
                                'rel'  => 'http://webfinger.net/rel/avatar',
                                'href' => $user->getIcon()
                            ),
                            array(
                                'rel'  => 'http://webfinger.net/rel/profile-page',
                                'href' => $user->getURL()
                            )
                        );
                    }

                    $event->setResponse($links);

                });

            }

            function getFollowing()
            {

            }

            function getOwner()
            {
                return $this;
            }

            function getOwnerID()
            {
                return $this->getUUID();
            }

            /**
             * Retrieve a text description of this user
             * @return string
             */
            function getDescription()
            {
                if (!empty($this->profile['description'])) {
                    return $this->profile['description'];
                }

                return '';
            }

            /**
             * Sets this user's username handle (and balks if someone's already using it)
             * @param string $handle
             * @return true|false True or false depending on success
             */

            function setHandle($handle)
            {
                $handle = trim($handle);
                $handle = strtolower($handle);
                if (!empty($handle)) {
                    if (!self::getByHandle($handle)) {
                        $this->handle = $handle;
                    }
                }

                return false;
            }

            /**
             * Retrieve's this user's handle
             * @return string
             */

            function getHandle()
            {
                return $this->handle;
            }

            /**
             * Returns this user's unique key for use with the API, and generates a new one if they don't
             * have one yet
             * @return string
             */
            function getAPIkey() {
                if (!empty($this->apikey)) {
                    return $this->apikey;
                }
                return $this->generateAPIkey();
            }

            /**
             * Generate a semi-random API key for this user, and then return it
             * @return string
             */
            function generateAPIkey() {
                $apikey = md5(time() . \Idno\Core\site()->config()->host . \Idno\Core\site()->config()->email . rand(0,999999) . rand (0,999999) . microtime());
                $apikey = strtolower(substr(base64_encode($apikey), 12, 16));
                $this->apikey = $apikey;
                $this->save();
                return $apikey;
            }

            /**
             * Is this user an admin?
             * @return bool
             */
            function isAdmin()
            {
                if (!empty($this->admin)) return true;

                return false;
            }

            /**
             * Set this user's site administrator status
             * @param bool $admin
             */
            function setAdmin($admin)
            {
                if ($admin == true) {
                    $this->admin = true;
                } else {
                    $this->admin = false;
                }
            }

            /**
             * Retrieve the URI to this user's avatar icon image
             * (if none has been saved, a default is returned)
             *
             * @return string
             */
            function getIcon()
            {
                $response = \Idno\Core\site()->triggerEvent('icon', array('object' => $this));
                if (!empty($response) && $response !== true) {
                    return $response;
                }
                if (!empty($this->icon)) {
                    return \Idno\Core\site()->config()->url . 'file/' . $this->icon;
                }

                return \Idno\Core\site()->config()->url . 'gfx/users/default.png';
            }

            /**
             * Retrieve the URL required to edit this user
             * @return string
             */
            function getEditURL()
            {
                return \Idno\Core\site()->config()->url . 'profile/' . $this->getHandle() . '/edit';
            }

            /**
             * Sets the built-in password property to a safe hash (if the
             * password is acceptable)
             *
             * @param string $password
             * @return true|false
             */
            function setPassword($password)
            {
                if (!empty($password)) {
                    $this->password = \password_hash($password, PASSWORD_BCRYPT);

                    return true;
                }

                return false;
            }

            /**
             * Verifies that the supplied password matches this user's password
             *
             * @param string $password
             * @return true|false
             */
            function checkPassword($password)
            {
                return \password_verify($password, $this->password);
            }

            /**
             * Does this user have everything he or she needs to be a fully-fledged
             * idno member? This method checks to make sure the minimum number of
             * fields are filled in.
             *
             * @return true|false
             */

            function isComplete()
            {
                $handle = $this->getHandle();
                $title  = $this->getTitle();
                if (!empty($handle) && !empty($title)) return true;
                return false;
            }

            /**
             * Get the profile URL for this user
             * @return string
             */
            function getURL()
            {
                return \Idno\Core\site()->config()->url . 'profile/' . $this->getHandle();
            }

            /**
             * Get a list of user IDs that this user marks as following
             * @return array|null
             */
            function getFollowingIDs()
            {
                if (!empty($this->following)) {
                    return $this->following;
                } else {
                    return [];
                }
            }

            /**
             * Given a user entity (or a UUID), marks them as being followed by this user.
             * Remember to save this user entity.
             *
             * @param \Idno\Entities\User|string $user
             * @return bool
             */
            function addFollowing($user)
            {
                if ($user instanceof \Idno\Entities\User) {
                    $users = $this->getFollowingIDs();
                    if (!in_array($user->getUUID(), $users)) {
                        $users[]     = $user->getUUID();
                        $this->following = $users;

                        return true;
                    }
                }

                return false;
            }

            /**
             * Given a user entity (or a UUID), removes them from this user's followed list.
             * Remember to save this user entity.
             *
             * @param \Idno\Entities\User|string $user
             * @return bool
             */
            function removeFollowing($user)
            {
                if ($user instanceof \Idno\Entities\User) {
                    $users       = $this->getFollowingIDs();
                    $users       = array_diff($users, [$user->getUUID()]);
                    $this->following = $users;

                    return true;
                }

                return false;
            }

            /**
             * Is the given user a followed by this user?
             *
             * @param \Idno\Entities\User|string $user
             * @return bool
             */
            function isFollowing($user)
            {
                if ($user instanceof \Idno\Entities\User) {
                    if (in_array($user->getUUID(), $this->getFollowingIDs())) {
                        return true;
                    }
                }

                return false;
            }

            /**
             * Is the given user following this user?
             *
             * @param \Idno\Entities\User $user
             * @return bool
             */
            function isFollowedBy($user)
            {
                if ($user instanceof \Idno\Entities\User) {
                    if ($user->isFollowing($this)) {
                        return true;
                    }
                }

                return false;
            }

            /**
             * Array of access groups that this user can *read* entities
             * from
             *
             * @return array
             */

            function getReadAccessGroups()
            {
                return $this->getXAccessGroups('read');
            }

            /**
             * Array of access groups that this user can *write* entities
             * to
             *
             * @return array
             */

            function getWriteAccessGroups()
            {
                return $this->getXAccessGroups('write');
            }

            /**
             * Get an array of access groups that this user has arbitrary permissions for
             *
             * @param string $permission The type of permission
             * @return array
             */
            function getXAccessGroups($permission)
            {
                $return = array('PUBLIC', $this->getUUID());
                if ($groups = \Idno\Core\site()->db()->getObjects('Idno\\Entities\\AccessGroup', array('members.' . $permission => $this->getUUID()), null, PHP_INT_MAX, 0)) {
                    $return = array_merge($return, $groups);
                }

                return $return;
            }

            /**
             * Array of access group IDs that this user can *read* entities
             * from
             *
             * @return array
             */

            function getReadAccessGroupIDs()
            {
                return $this->getXAccessGroupIDs('read');
            }

            /**
             * Array of access group IDs that this user can *write* entities
             * to
             *
             * @return type
             */

            function getWriteAccessGroupIDs()
            {
                return $this->getXAccessGroupIDs('write');
            }

            /**
             * Get an array of access group IDs that this user has an arbitrary permission for
             *
             * @param string $permission Permission type
             * @return array
             */
            function getXAccessGroupIDs($permission)
            {
                $return = array('PUBLIC', $this->getUUID());
                if ($groups = \Idno\Core\site()->db()->getRecords(array('uuid' => true),
                    array(
                         'entity_subtype'         => 'Idno\\Entities\\AccessGroup',
                         'members.' . $permission => $this->getUUID()),
                    PHP_INT_MAX,
                    0)
                ) {
                    foreach ($groups as $group) {
                        $return[] = $group['uuid'];
                    }
                }

                return $return;
            }

            /**
             * Users are activity streams objects of type "person".
             *
             * @return string
             */
            function getActivityStreamsObjectType()
            {
                $uuid = $this->getUUID();
                if (!empty($uuid))
                    return 'person';

                return false;
            }

            /**
             * Hook to provide a method of notifying a user - for example, sending an email or displaying a popup.
             *
             * @param string $message The message to notify the user with.
             * @param string $long_message Optionally, a longer version of the message with more detail.
             * @param \Idno\Common\Entity|null $object Optionally, an object to pass
             * @param array|null $params Optionally, any parameters to pass to the process. NB: this should be used rarely.
             */
            public function notify($message, $long_message = null, $object = null, $params = null)
            {
                return \Idno\Core\site()->triggerEvent('notify', [
                                                                 'user'         => $this,
                                                                 'message'      => $message,
                                                                 'long_message' => $long_message,
                                                                 'object'       => $object,
                                                                 'parameters'   => $params
                                                                 ]);
            }

            /**
             * Save form input
             * @param \Idno\Common\Page $page
             * @return bool|\Idno\Common\false|\Idno\Common\true|\Idno\Core\false|\Idno\Core\MongoID|null
             */
            function saveDataFromInput()
            {

                if (!$this->canEdit()) return false;
                $this->profile = \Idno\Core\site()->currentPage()->getInput('profile');
                if (!empty($_FILES['avatar'])) {
                    if (in_array($_FILES['avatar']['type'], array('image/png', 'image/jpg', 'image/jpeg', 'image/gif'))) {
                        if (getimagesize($_FILES['avatar']['tmp_name'])) {
                            if ($icon = \Idno\Entities\File::createThumbnailFromFile($_FILES['avatar']['tmp_name'], $_FILES['avatar']['name'], 300)) {
                                $this->icon = (string)$icon;
                            } else if ($icon = \Idno\Entities\File::createFromFile($_FILES['avatar']['tmp_name'], $_FILES['avatar']['name'])) {
                                $this->icon = (string)$icon;
                            }
                        }
                    }
                }

                return $this->save();

            }

            public function jsonSerialize()
            {
                $data          = parent::jsonSerialize();
                $data['image'] = ['url' => $this->getIcon()];

                return $data;
            }

            /**
             * Retrieves user by handle
             * @param string $handle
             * @return User|false Depending on success
             */
            static function getByHandle($handle)
            {
                if ($result = \Idno\Core\site()->db()->getObjects('Idno\\Entities\\User', array('handle' => $handle), null, 1)) {
                    foreach ($result as $row) {
                        return $row;
                    }
                }

                return false;
            }

            /**
             * Retrieves user by email address
             * @param string $email
             * @return User|false Depending on success
             */
            static function getByEmail($email)
            {
                if ($result = \Idno\Core\site()->db()->getObjects('Idno\\Entities\\User', array('email' => $email), null, 1)) {
                    foreach ($result as $row) {
                        return $row;
                    }
                }

                return false;
            }



        }

    }