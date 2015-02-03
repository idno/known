<?php

    /**
     * Defines built-in log out functionality
     */

    namespace Idno\Pages\Session {

        /**
         * Default class to serve the homepage
         */
        class Logout extends \Idno\Common\Page
        {

            function getContent()
            {
            }

            function postContent()
            {
                \Idno\Core\site()->triggerEvent('logout/success', array('user' => \Idno\Core\site()->session()->currentUser())); // Trigger an event for auditing
                
                $result = \Idno\Core\site()->session()->logUserOff();
                \Idno\Core\site()->session()->addMessage("You've signed out. See you soon!");
                $this->forward($_SERVER['HTTP_REFERER']);

                return $result;
            }

        }

    }