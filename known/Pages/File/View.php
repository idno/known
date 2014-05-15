<?php

    /**
     * Generic, backup viewer for entities
     */

    namespace known\Pages\File {

        /**
         * Default class to serve the homepage
         */
        class View extends \known\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {
                if (!empty($this->arguments[0])) {
                    $object = \known\Entities\File::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward(); // TODO: 404

                $headers = apache_request_headers();
                if (isset($headers['If-Modified-Since'])) {
                    if (strtotime($headers['If-Modified-Since']) < time() - 600) {
                        header('HTTP/1.1 304 Not Modified');
                        exit;
                    }
                }

                header("Pragma: public");
                header('Expires: ' . date(\DateTime::RFC1123, time() + (86400 * 365))); // Cache files for a year!
                if (!empty($object->file['mime_type'])) {
                    header('Content-type: ' . $object->file['mime_type']);
                } else {
                    header('Content-type: application/data');
                }
                echo $object->getBytes();

            }

        }

    }