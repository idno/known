<?php

    /**
     * User profile
     */

    namespace Idno\Pages\Session {

        /**
         * Default class to serve the homepage
         */
        class CurrentUser extends \Idno\Common\Page
        {

            // Handle GET requests to the entity

            function postContent()
            {
                return $this->getContent();
            }

            // Handle POST requests to the entity

            function getContent()
            {
                $user = \Idno\Core\Idno::site()->session()->currentUser();
                if (empty($user)) $this->noContent();

                $this->setPermalink(); // This is a permalink

                $t = \Idno\Core\Idno::site()->template();
                $t->__(array(

                    'title'       => $user->getTitle(),
                    'body'        => $t->__(array('user' => $user, 'items' => array(), 'count' => 0, 'offset' => 0))->draw('entity/User/profile'),
                    'description' =>  \Idno\Core\Idno::site()->language()->_('The %s profile for %s', \Idno\Core\Idno::site()->config()->title, $user->getTitle())

                ))->drawPage();
            }

        }

    }