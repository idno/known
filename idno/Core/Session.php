<?php

/**
 * Session management class
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class Session extends \Idno\Common\Component
    {

        function init()
        {
            session_name(site()->config->sessionname);
            session_start();

            // Session login / logout
            site()->addPageHandler('/session/login', '\Idno\Pages\Session\Login');
            site()->addPageHandler('/session/logout', '\Idno\Pages\Session\Logout');
        }

        /**
         * Is a user logged into the current session?
         * @return true|false
         */
        function isLoggedIn()
        {
            if (!empty($_SESSION['user']) && $_SESSION['user'] instanceof \Idno\Entities\User) {
                return true;
            }
            return false;
        }

        /**
         * Wrapper function for isLoggedIn()
         * @see Idno\Core\Session::isLoggedIn()
         * @return true|false
         */

        function isLoggedOn()
        {
            return $this->isLoggedIn();
        }

        /**
         * Returns the currently logged-in user, if any
         * @return Idno\Entities\User
         */

        function currentUser()
        {
            if (!empty($_SESSION['user']))
                return $_SESSION['user'];
            return false;
        }

        /**
         * Get the UUID of the currently logged-in user, or false if
         * we're logged out
         *
         * @return mixed
         */

        function currentUserUUID()
        {
            if ($this->isLoggedOn()) {
                return $this->currentUser()->getUUID();
            }
            return false;
        }

        /**
         * Adds a message to the queue to be delivered to the user as soon as is possible
         * @param string $message The text of the message
         * @param string $message_type This type of message; this will be added to the displayed message class, or returned as data
         */

        function addMessage($message, $message_type = 'alert-info')
        {
            if (empty($_SESSION['messages'])) $_SESSION['messages'] = array();
            $_SESSION['messages'][] = array('message' => $message, 'message_type' => $message_type);
        }

        /**
         * Retrieve any messages waiting for the user in the session
         * @return array
         */
        function getMessages()
        {
            if (!empty($_SESSION['messages'])) {
                return $_SESSION['messages'];
            } else {
                return array();
            }
        }

        /**
         * Remove any messages from the session
         */
        function flushMessages()
        {
            $_SESSION['messages'] = array();
        }

        /**
         * Retrieve any messages from the session, remove them from the session, and return them
         * @return array
         */
        function getAndFlushMessages()
        {
            $messages = $this->getMessages();
            $this->flushMessages();
            return $messages;
        }

        /**
         * Get access groups the current user is allowed to write to
         * @return array
         */

        function getWriteAccessGroups()
        {
            if ($this->isLoggedOn())
                return $this->currentUser()->getWriteAccessGroups();
            return array();
        }

        /**
         * Get IDs of the access groups the current user is allowed to write to
         * @return array
         */

        function getWriteAccessGroupIDs()
        {
            if ($this->isLoggedOn())
                return $this->currentUser()->getWriteAccessGroups();
            return array();
        }

        /**
         * Get access groups the current user (if any) is allowed to read from
         * @return array
         */

        function getReadAccessGroups()
        {
            if ($this->isLoggedOn())
                return $this->currentUser()->getReadAccessGroups();
            return array('PUBLIC');
        }

        /**
         * Get IDs of the access groups the current user (if any) is allowed to read from
         * @return array
         */

        function getReadAccessGroupIDs()
        {
            if ($this->isLoggedOn())
                return $this->currentUser()->getReadAccessGroupIDs();
            return array('PUBLIC');
        }

        /**
         * Log the specified user on (note that this is NOT the same as taking the user's auth credentials)
         *
         * @param Idno\Entities\User $user
         * @return Idno\Entities\User
         */

        function logUserOn(\Idno\Entities\User $user)
        {
            $_SESSION['user'] = $user;
            return $user;
        }

        /**
         * Log the current session user off
         * @return true
         */

        function logUserOff()
        {
            unset($_SESSION['user']);
            return true;
        }

        /**
         * Checks HTTP request headers to see if the request has been properly
         * signed for API access, and if so, log the user on
         * @todo make this complete
         *
         * @return true|false Whether the user could be logged in
         */

        function APIlogin()
        {
            return false;
        }

    }

}