<?php

    namespace Idno\Files {

        /**
         * Class File
         * Represents a single file in the system
         * @package Idno\Files
         */
        abstract class File
        {

            public $metadata = [];
            public $_id;
            public $file = [];

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
             * Returns this file's filename
             * @return string
             */
            abstract function getFilename();

            /**
             * Writes the contents of this file to a location specified in $path
             * @param string $path
             * @return mixed
             */
            abstract function write($path);

            /* Delete this file
             * @return bool
             */
            abstract function delete();

        }

    }