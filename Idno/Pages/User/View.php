<?php

    /**
     * User profile
     */

    namespace Idno\Pages\User {

        /**
         * Default class to serve the homepage
         */
        class View extends \Idno\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $user = \Idno\Entities\User::getByHandle($this->arguments[0]);
                }
                if (empty($user)) {
                    $this->noContent();
                }

                // Users own their own profiles
                $this->setOwner($user);

                //$this->setPermalink();  // This is a permalink
                $offset = (int)$this->getInput('offset');
                $count  = \Idno\Entities\ActivityStreamPost::count(array('owner' => $user->getUUID()));
                $feed   = \Idno\Entities\ActivityStreamPost::get(array('owner' => $user->getUUID()), [], \Idno\Core\site()->config()->items_per_page, $offset);

                $t = \Idno\Core\site()->template();
                $t->__(array(

                    'title'       => $user->getTitle(),
                    'body'        => $t->__(array('user' => $user, 'items' => $feed, 'count' => $count, 'offset' => $offset))->draw('entity/User/profile'),
                    'description' => 'The ' . \Idno\Core\site()->config()->title . ' profile for ' . $user->getTitle()

                ))->drawPage();
            }

            // Handle POST requests to the entity

            function postContent()
            {
                if (!empty($this->arguments[0])) {
                    $user = \Idno\Entities\User::getByHandle($this->arguments[0]);
                }
                if (empty($user)) $this->forward(); // TODO: 404
                if ($user->saveDataFromInput($this)) {
                    \Idno\Core\site()->session()->addMessage($user->getTitle() . ' was saved.');
                    $this->forward($user->getURL());
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

            // Handle DELETE requests to the entity

            function deleteContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->delete()) {
                    \Idno\Core\site()->session()->addMessage($object->getTitle() . ' was deleted.');
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }