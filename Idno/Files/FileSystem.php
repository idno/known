<?php

namespace Idno\Files {

    /*
     * Class FileSystem
     * Represents a file system capable of storing files
     * @package Idno\Files
     */

    abstract class FileSystem
    {

        // http://php.net/manual/en/features.file-upload.errors.php
        public static $FILE_UPLOAD_ERROR_CODES = array(
            UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success',
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        );
        
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

