<?php

    /**
     * Access group representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        class AccessGroup extends \Idno\Common\Entity
        {

            /**
             * On initial creation, make sure access groups have a members property
             * @return mixed
             */
            function __construct()
            {
                $this->members = array(
                    'read'  => array(\Idno\Core\Idno::site()->session()->currentUser()->getUUID()),
                    'write' => array(\Idno\Core\Idno::site()->session()->currentUser()->getUUID()),
                    'admin' => array(\Idno\Core\Idno::site()->session()->currentUser()->getUUID())
                );

                return parent::__construct();
            }

            /**
             * Can the specified user (or the currently logged-in user) access
             * content in this access group?
             *
             * @param string $user_id The user ID (optional)
             * @return true|false
             */
            function canRead($user_id = '')
            {
                if (empty($user_id)) $user_id = \Idno\Core\Idno::site()->session()->currentUser()->uuid;
                if ($this->getOwnerID() == $user_id) return true;
                if ($this->isMember($user_id)) return true;
                if ($this->access == 'PUBLIC') return true;

                return false;
            }

            /**
             * Is the specified user (or the currently logged-in user) a member
             * of this access group?
             *
             * @param type $user_id
             * @return type
             */
            function isMember($user_id = '', $access = 'read')
            {
                if (empty($user_id)) $user_id = \Idno\Core\Idno::site()->session()->currentUser()->uuid;
                if (!empty($this->members[$access]) && is_array($this->members[$access]) && array_search($user_id, $this->members[$access])) {
                    return true;
                }

                return false;
            }

            /**
             * Can the specified user (or the currently logged-in user) publish
             * content to this access group?
             *
             * @param string $user_id The user ID (optional)
             * @return true|false
             */
            function canPublish($user_id = '')
            {
                if (empty($user_id)) $user_id = \Idno\Core\Idno::site()->session()->currentUser()->uuid;
                if ($this->getOwnerID() == $user_id) return true;
                if ($this->isMember($user_id, 'write')) return true;

                return false;
            }

            /**
             * Adds a specified user to the access group
             *
             * @param string $user_id The user UUID
             * @return true|false
             */
            function addMember($user_id, $access = 'read')
            {
                if ($this->canEdit()) {
                    if (($user = \Idno\Core\Idno::site()->db()->getObject($user_id)) && ($user instanceof User)) {
                        $this->members[$access][] = $user_id;

                        return true;
                    }
                }

                return false;
            }

            /**
             * Can the specified user (or the currently logged-in user) administer
             * this access group?
             *
             * @param string $user_id The user ID (optional)
             * @return true|false
             */
            function canEdit($user_id = '')
            {
                if (empty($user_id)) $user_id = \Idno\Core\Idno::site()->session()->currentUser()->uuid;
                if ($this->getOwnerID() == $user_id) return true;
                if ($this->isMember($user_id, 'admin')) return true;

                return false;
            }

            /**
             * Removes a specified user from the access group
             *
             * @param string $user_id The user UUID
             * @return true|false
             */
            function removeMember($user_id, $access = 'read')
            {
                if (!empty($this->members) && is_array($this->members) && $key = array_search($user_id, $this->members)) {
                    unset($this->members[$access][$key]);

                    return true;
                }

                return false;
            }

        }

    }
