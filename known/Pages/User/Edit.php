<?php

    /**
     * Edit the user's profile
     */

    namespace known\Pages\User {

        /**
         * Default class to serve the homepage
         */
        class Edit extends \known\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $user = \known\Entities\User::getByHandle($this->arguments[0]);
                }
                if (empty($user)) $this->forward(); // TODO: 404

                $t = \known\Core\site()->template();
                $t->__(array(

                    'title' => 'Edit profile: ' . $user->getTitle(),
                    'body'  => $t->__(array('user' => $user))->draw('entity/User/edit')

                ))->drawPage();
            }

        }

    }