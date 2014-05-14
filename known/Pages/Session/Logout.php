<?php

    /**
     * Defines built-in log out functionality
     */

    namespace known\Pages\Session {

        /**
         * Default class to serve the homepage
         */
        class Logout extends \known\Common\Page
        {

            function getContent()
            {
            }

            function postContent()
            {
                $result = \known\Core\site()->session()->logUserOff();
                \known\Core\site()->session()->addMessage("You've signed out. See you soon!");
                $this->forward($_SERVER['HTTP_REFERER']);

                return $result;
            }

        }

    }