<?php

    /**
     * Session management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        use Idno\Entities\User;

        class Session extends \Idno\Common\Component
        {

            function init()
            {

                ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // Persistent cookies
                ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30); // Garbage collection to match
                ini_set('session.cookie_httponly', true); // Restrict cookies to HTTP only (help reduce XSS attack profile)

                site()->db()->handleSession();

                session_name(site()->config->sessionname);
                session_start();
                session_cache_limiter('public');
                session_regenerate_id();

                // Session login / logout
                site()->addPageHandler('/session/login', '\Idno\Pages\Session\Login', true);
                site()->addPageHandler('/session/logout', '\Idno\Pages\Session\Logout');
                site()->addPageHandler('/currentUser/?', '\Idno\Pages\Session\CurrentUser');

                // Update the session on save, this is a shim until #46 is fixed properly with #49
                \Idno\Core\site()->addEventHook('save', function (\Idno\Core\Event $event) {

                    $object = $event->data()['object'];
                    if ((!empty($object)) && ($object instanceof \Idno\Entities\User) // Object is a user
                        && ((!empty($_SESSION['user'])) && ($object->getUUID() == $_SESSION['user']->getUUID()))
                    ) // And we're not trying a user change (avoids a possible exploit)
                    {
                        $_SESSION['user'] = $object;
                    }

                });
            }

            /**
             * Kill the session.
             */
            function finishEarly()
            {
                session_write_close();
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
             * Wrapper function for isLoggedIn()
             * @see Idno\Core\Session::isLoggedIn()
             * @return true|false
             */

            function isLoggedOn()
            {
                return $this->isLoggedIn();
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
             * Returns the currently logged-in user, if any
             * @return \Idno\Entities\User
             */

            function currentUser()
            {
                if (!empty($_SESSION['user']))
                    return $_SESSION['user'];

                return false;
            }

            /**
             * Adds a message to the queue to be delivered to the user as soon as is possible
             * @param string $message The text of the message
             * @param string $message_type This type of message; this will be added to the displayed message class, or returned as data
             */

            function addMessage($message, $message_type = 'alert-info')
            {
                if (empty($_SESSION['messages'])) {
                    $_SESSION['messages'] = [];
                }
                $_SESSION['messages'][] = array('message' => $message, 'message_type' => $message_type);
            }
            
            /**
             * Error message wrapper for addMessage()
             * @param type $message
             */
            function addErrorMessage($message) {
                $this->addMessage($message, 'alert-error');
            }

            /**
             * Adds a message to the queue to be delivered to the user as soon as is possible, ensuring it's at the beginning of the list
             * @param string $message The text of the message
             * @param string $message_type This type of message; this will be added to the displayed message class, or returned as data
             */

            function addMessageAtStart($message, $message_type = 'alert-info') {
                if (empty($_SESSION['messages'])) {
                    $_SESSION['messages'] = [];
                }
                array_unshift($_SESSION['messages'], array('message' => $message, 'message_type' => $message_type));
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
             * Retrieve any messages waiting for the user in the session
             * @return array
             */
            function getMessages()
            {
                if (!empty($_SESSION['messages'])) {
                    return $_SESSION['messages'];
                } else {
                    return [];
                }
            }

            /**
             * Remove any messages from the session
             */
            function flushMessages()
            {
                $messages                       = [];
                $_SESSION['messages']           = $messages;
                $_SESSION['last_message_flush'] = date('r', time());
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
                $group = array('PUBLIC');
                if ($this->isLoggedOn()) {
                    $group = $this->currentUser()->getReadAccessGroupIDs();
                }

                return $group;
            }

            /**
             * Log the current session user off
             * @return true
             */

            function logUserOff()
            {
                unset($_SESSION['user']);
                session_destroy();

                return true;
            }

            /**
             * Set a piece of session data
             * @param string $name
             * @param mixed $value
             */
            function set($name, $value)
            {
                $_SESSION[$name] = $value;
            }

            /**
             * Retrieve the session data with key $name, if it exists
             * @param string $name
             * @return mixed
             */
            function get($name)
            {
                if (!empty($_SESSION[$name])) {
                    return $_SESSION[$name];
                } else {
                    return false;
                }
            }

            /**
             * Remove data with key $name from the session
             * @param $name
             */
            function remove($name)
            {
                unset($_SESSION[$name]);
            }

            /**
             * Checks HTTP request headers to see if the request has been properly
             * signed for API access, and if so, log the user on and return the user
             *
             * @return \Idno\Entities\User|false The logged-in user, or false otherwise
             */

            function APIlogin()
            {

                if (!empty($_SERVER['HTTP_X_KNOWN_USERNAME']) && !empty($_SERVER['HTTP_X_KNOWN_SIGNATURE'])) {
                    if ($user = \Idno\Entities\User::getByHandle($_SERVER['HTTP_X_KNOWN_USERNAME'])) {
                        $key          = $user->getAPIkey();
                        $hmac         = trim($_SERVER['HTTP_X_KNOWN_SIGNATURE']);
                        $compare_hmac = base64_encode(hash_hmac('sha256', $_SERVER['REQUEST_URI'], $key, true));
                        if ($hmac == $compare_hmac) {
                            \Idno\Core\site()->session()->logUserOn($user);
                            \Idno\Core\site()->session()->setIsAPIRequest(true);

                            return $user;
                        }
                    }
                }

                return false;
            }

            /**
             * Log the specified user on (note that this is NOT the same as taking the user's auth credentials)
             *
             * @param \Idno\Entities\User $user
             * @return \Idno\Entities\User
             */

            function logUserOn(\Idno\Entities\User $user)
            {
                $return = $this->refreshSessionUser($user);

                \Idno\Core\site()->triggerEvent('user/auth', ['user' => $user]);

                return $return;
            }

            /**
             * Refresh the user currently stored in the session
             * @param \Idno\Entities\User $user
             * @return \Idno\Entities\User
             */
            function refreshSessionUser(\Idno\Entities\User $user)
            {
                if ($user = User::getByUUID($user->getUUID())) {
                    $_SESSION['user'] = $user;
                    return $user;
                }

                return false;
            }

            /**
             * Sets whether this session is an API request or a manual browse
             * @param boolean $is_api_request
             */
            function setIsAPIRequest($is_api_request)
            {
                $is_api_request             = (bool)$is_api_request;
                $_SESSION['is_api_request'] = $is_api_request;
            }

            /**
             * Is this session an API request?
             * @return bool
             */
            function isAPIRequest()
            {
                if (!empty($_SESSION['is_api_request'])) {
                    return true;
                }

                return false;
            }

            /**
             * If the current user isn't logged in and this isn't a public site, and this hasn't been defined as an
             * always-public page, forward to the login page!
             */
            function publicGatekeeper()
            {
                if (!site()->config()->isPublicSite()) {
                    if (!site()->session()->isLoggedOn()) {
                        $class = get_class(site()->currentPage());
                        if (!site()->isPageHandlerPublic($class)) {
                            site()->currentPage()->forward(site()->config()->getURL() . 'session/login/');
                        }
                    }
                }
            }

        }

    }