<?php

    namespace Idno\Files {

        /**
         * Class File
         * Represents a single file in the system
         * @package Idno\Files
         */
        abstract class File
        {

            public $metadata = array();
            public $_id;
            public $file = array();

            /**
             * Given a file size in bytes, converts it to a friendly version
             * @param $bytes
             * @return string
             */
            static function describeFileSize($bytes)
            {
                $units = array('B', 'KB', 'MB', 'GB', 'TB');

                $bytes = max($bytes, 0);
                $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow   = min($pow, count($units) - 1);

                $bytes /= (1 << (10 * $pow));

                return round($bytes, 2) . ' ' . $units[$pow];

            }

            /**
             * Retrieve the bytes associated with the file
             * @return mixed
             */
            abstract function getBytes();

            /**
             * Pass through bytes associated with the file
             * @return mixed
             */
            abstract function passThroughBytes();

            /**
             * Get a stream resource referencing the file
             * @return mixed
             */
            abstract function getResource();

            /**
             * Returns this file's filename
             * @return string
             */
            abstract function getFilename();
            
            /**
             * Return the file's size in bytes.
             * @return int
             */
            abstract function getSize();

            /* Delete this file
             * @return bool
             */

            /**
             * Writes the contents of this file to a location specified in $path
             * @param string $path
             * @return mixed
             */
            abstract function write($path);

            /**
             * Alias for delete
             * @return mixed
             */
            function remove()
            {
                return $this->delete();
            }

            abstract function delete();

        }

    }