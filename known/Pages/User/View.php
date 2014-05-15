<?php

    /**
     * User profile
     */

    namespace known\Pages\User {

        /**
         * Default class to serve the homepage
         */
        class View extends \known\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $user = \known\Entities\User::getByHandle($this->arguments[0]);
                }
                if (empty($user)) {
                    $this->noContent();
                }

                // Users own their own profiles
                $this->setOwner($user);

                //$this->setPermalink();  // This is a permalink
                $offset = (int)$this->getInput('offset');
                $count  = \known\Entities\ActivityStreamPost::count(array('owner' => $user->getUUID()));
                $feed   = \known\Entities\ActivityStreamPost::get(array('owner' => $user->getUUID()), [], \known\Core\site()->config()->items_per_page, $offset);

                $t = \known\Core\site()->template();
                $t->__(array(

                    'title'       => $user->getTitle(),
                    'body'        => $t->__(array('user' => $user, 'items' => $feed, 'count' => $count, 'offset' => $offset))->draw('entity/User/profile'),
                    'description' => 'The ' . \known\Core\site()->config()->title . ' profile for ' . $user->getTitle()

                ))->drawPage();
            }

            // Handle POST requests to the entity

            function postContent()
            {
                if (!empty($this->arguments[0])) {
                    $user = \known\Entities\User::getByHandle($this->arguments[0]);
                }
                if (empty($user)) $this->forward(); // TODO: 404
                if ($user->saveDataFromInput($this)) {
                    \known\Core\site()->session()->addMessage($user->getTitle() . ' was saved.');
                    $this->forward($user->getURL());
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

            // Handle DELETE requests to the entity

            function deleteContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->delete()) {
                    \known\Core\site()->session()->addMessage($object->getTitle() . ' was deleted.');
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }