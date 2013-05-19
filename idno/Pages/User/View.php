<?php

    /**
     * Generic, backup viewer for entities
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
                if (empty($user)) $this->forward(); // TODO: 404

                $feed = \Idno\Entities\ActivityStreamPost::get(array('owner' => $user->getUUID()));

                $t = \Idno\Core\site()->template();
                $t->__(array(

                    'title' => $user->getTitle(),
                    'body' => $t->__(array('user' => $user, 'feed' => $feed))->draw('entity/User/profile')

                ))->drawPage();
            }

            // Handle POST requests to the entity

            function postContent() {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if ($object->saveDataFromInput()) {
                    \Idno\Core\site()->session()->addMessage($object->getTitle() . ' was saved.');
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

            // Handle DELETE requests to the entity

            function deleteContent() {
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