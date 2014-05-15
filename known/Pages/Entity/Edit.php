<?php

    /**
     * Generic, backup viewer for entities
     */

    namespace known\Pages\Entity {

        /**
         * Default class to serve the homepage
         */
        class Edit extends \known\Common\Page
        {

            // Handle GET requests to the homepage

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Common\Entity::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404
                if (!$object->canEdit()) $this->forward($object->getURL());

                $t = \known\Core\site()->template();
                $t->__(array(

                    'title' => $object->getTitle(),
                    'body'  => $object->drawEdit()

                ))->drawPage();
            }

        }

    }