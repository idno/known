<?php

    /**
     * Generic, backup viewer for entities
     */

    namespace Idno\Pages\File {

        /**
         * Default class to serve the homepage
         */
        class View extends \Idno\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \Idno\Entities\File::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404

                if (!empty($object->mime_type)) {
                    header('Content-type: ' . $object->mime_type);
                } else {
                    header('Content-type: application/data');
                }
                echo $object->getBytes();

            }

        }

    }