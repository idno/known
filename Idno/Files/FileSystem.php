<?php

namespace Idno\Files {

    /*
     * Class FileSystem
     * Represents a file system capable of storing files
     * @package Idno\Files
     */

    abstract class FileSystem
    {

        /**
         * Find a file.
         * @param $_id
         * @return mixed
         */
        abstract function findOne($_id);

        /**
         * Store the file at $file_path with $metadata and $options
         * @param $file_path
         * @param $metadata
         * @param $options
         * @return id of file
         */
        abstract function storeFile($file_path, $metadata, $options);

    }

}

