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
            \Idno\Core\site()->addEventHook('webfinger',function(\Idno\Core\Event $event) {

                $user =  $event->data()['object'];

                $links = $event->response();
                if (empty($links)) $links = array();

                if ($user instanceof User) {
                    $links = array(
                        array(
                            'rel' => 'http://webfinger.net/rel/avatar',
                            'href' => $user->getIcon()
                        ),
                        array(
                            'rel' => 'http://webfinger.net/rel/profile-page',
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

        function getOwner() {
            return $this;
        }

        function getOwnerID() {
            return $this->getUUID();
        }

        /**
         * Retrieve a text description of this user
         * @return string
         */
        function getDescription() {
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
         * Is this user an admin?
         * @return bool
         */
        function isAdmin() {
            if (!empty($this->admin)) return true;
            return false;
        }

        /**
         * Set this user's site administrator status
         * @param bool $admin
         */
        function setAdmin($admin) {
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
        function getIcon() {
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
        function getEditURL() {
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
            $title = $this->getTitle();
            if (!empty($handle) && !empty($title)) return true;
        }

        /**
         * Get the profile URL for this user
         * @return string
         */
        function getURL() {
            return \Idno\Core\site()->config()->url . 'profile/' . $this->getHandle();
        }

        /**
         * Get a list of user IDs that this user marks as a friend
         * @return array|null
         */
        function getFriendIDs() {
            if (!empty($this->friends)) {
                return $this->friends;
            } else {
                return [];
            }
        }

        /**
         * Given a user entity, marks them as being a friend of this user.
         * Remember to save this user entity.
         *
         * @param \Idno\Entities\User $friend
         * @return bool
         */
        function addFriend($friend) {
            if ($friend instanceof \Idno\Entities\User) {
                $friends = $this->getFriendIDs();
                if (!in_array($friend->getUUID(), $friends)) {
                    $friends[] = $friend->getUUID();
                    $this->friends = $friends;
                    return true;
                }
            }
            return false;
        }

        /**
         * Given a user entity, removes them from this user's friends list.
         * Remember to save this user entity.
         *
         * @param \Idno\Entities\User $friend
         * @return bool
         */
        function removeFriend($friend) {
            if ($friend instanceof \Idno\Entities\User) {
                $friends = $this->getFriendIDs();
                $friends = array_diff($friends, [$friend->getUUID()]);
                $this->friends = $friends;
                return true;
            }
            return false;
        }

        /**
         * Is the given user a friend of this user?
         *
         * @param \Idno\Entities\User $friend
         * @return bool
         */
        function isFriend($friend) {
            if ($friend instanceof \Idno\Entities\User) {
                if (in_array($friend->getUUID(), $this->getFriendIDs())) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Does the given user list this user as a friend?
         *
         * @param \Idno\Entities\User $friend
         * @return bool
         */
        function isFriendOf($friend) {
            if ($friend instanceof \Idno\Entities\User) {
                if ($friend->isFriend($this)) {
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
                    'entity_subtype' => 'Idno\\Entities\\AccessGroup',
                    'members.' . $permission => $this->getUUID()),
                PHP_INT_MAX,
                0)
            ) {
                foreach ($groups as $group) {
                    $return[] = $group->uuid;
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
         */
        public function notify() {
             return \Idno\Core\site()->triggerEvent('notify', ['object' => $this]);
        }
        
        /**
         * Save form input
         * @param \Idno\Common\Page $page
         * @return bool|\Idno\Common\false|\Idno\Common\true|\Idno\Core\false|\Idno\Core\MongoID|null
         */
        function saveDataFromInput() {

            if (!$this->canEdit()) return false;
            $this->profile = \Idno\Core\site()->currentPage()->getInput('profile');
            return $this->save();

        }

        public function jsonSerialize() {
            $data = parent::jsonSerialize();
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