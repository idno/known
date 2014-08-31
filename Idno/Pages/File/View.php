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

                if (!function_exists('getallheaders')) {
                    function getallheaders()
                    {
                        $headers = '';
                        foreach ($_SERVER as $name => $value) {
                            if (substr($name, 0, 5) == 'HTTP_') {
                                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                            }
                        }

                        return $headers;
                    }
                }

                $headers = getallheaders();
                if (isset($headers['If-Modified-Since'])) {
                    if (strtotime($headers['If-Modified-Since']) < time() - 600) {
                        header('HTTP/1.1 304 Not Modified');
                        exit;
                    }
                }

                //header("Pragma: public");
                header('Expires: ' . date(\DateTime::RFC1123, time() + (86400 * 30))); // Cache files for 30 days!
                if (!empty($object->file['mime_type'])) {
                    header('Content-type: ' . $object->file['mime_type']);
                } else {
                    header('Content-type: application/data');
                }
                //header('Accept-Ranges: bytes');
                //header('Content-Length: ' . filesize($object->getSize()));
                if (is_callable([$object, 'passThroughBytes'])) {
                    $object->passThroughBytes();
                } else {
                    echo $object->getBytes();
                }

            }

        }

    }