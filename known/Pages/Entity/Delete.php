<?php

    /**
     * Generic, backup viewer for entities
     */

    namespace known\Pages\Entity {

        /**
         * Default class to serve the homepage
         */
        class Delete extends \known\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404

                $t = \known\Core\site()->template();
                $t->__(array(

                    'title' => $object->getTitle(),
                    'body'  => $object->draw()

                ))->drawPage();
            }

            // Handle POST requests to the entity

            function postContent()
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