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

                if (empty($object)) $this->noContent();


                session_write_close();  // Close the session early

                //header("Pragma: public");

                // Determine uploaded timestamp
                if ($object instanceof \MongoGridFSFile) {
                    $upload_ts = $object->file['uploadDate']->sec;
                } else if (!empty($object->updated)) {
                    $upload_ts = $object->updated;
                } else if (!empty($object->created)) {
                    $upload_ts = $object->created;
                } else {
                    $upload_ts = time();
                }

                header("Pragma: public");
                header("Cache-Control: public");
                header('Expires: ' . date(\DateTime::RFC1123, time() + (86400 * 30))); // Cache files for 30 days!
                $this->setLastModifiedHeader($upload_ts);
                if (!empty($object->file['mime_type'])) {
                    header('Content-type: ' . $object->file['mime_type']);
                } else {
                    header('Content-type: application/data');
                }
                //header('Accept-Ranges: bytes');
                //header('Content-Length: ' . filesize($object->getSize()));

                $headers = $this->getallheaders();
                if (isset($headers['If-Modified-Since'])) {
                    if (strtotime($headers['If-Modified-Since']) <= $upload_ts) { //> time() - (86400 * 30)) {
                        header('HTTP/1.1 304 Not Modified');
                        exit;
                    }
                }

                if (is_callable(array($object, 'passThroughBytes'))) {
                    $object->passThroughBytes();
                } else {
                    if ($stream = $object->getResource()) {
                        while (!feof($stream)) {
                            echo fread($stream, 8192);
                        }
                    }
                }

            }

        }

    }