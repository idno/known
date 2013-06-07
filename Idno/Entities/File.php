<?php

    /**
     * User-created file representation
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Entities {

        class File
        {

            /**
             * Return the MIME type associated with this file
             * @return null|string
             */
            function getMimeType() {
                $mime_type = $this->mime_type;
                if (!empty($mime_type)) {
                    return $this->mime_type;
                }
                return 'application/octet-stream';
            }

            /**
             * Get the publicly visible filename associated with this file
             * @return string
             */
            function getURL() {
                if (!empty($this->_id)) {
                    return \Idno\Core\site()->config()->url . 'file/' . $this->_id . '/' . urlencode($this->filename);
                }
                return '';
            }

            function delete() {
                // TODO deleting files would be good ...
            }

            /**
             * Save a file to the filesystem and return the ID
             *
             * @param string $file_path Full local path to the file
             * @param string $filename Filename to store
             * @param string $mime_type MIME type associated with the file
             * @param bool $return_object Return the file object? If set to false (as is default), will return the ID
             * @return bool|\MongoID Depending on success
             */
            public static function createFromFile($file_path, $filename, $mime_type = 'application/octet-stream', $return_object = false) {
                if (file_exists($file_path) && !empty($filename)) {
                    if ($fs = \Idno\Core\site()->db()->getFilesystem()) {
                        $file = new File();
                        $metadata = array(
                            'filename' => $filename,
                            'mime_type' => $mime_type
                        );
                        if ($id = $fs->storeFile($file_path, $metadata, $metadata)) {
                            if (!$return_object) {
                                return $id;
                            } else {
                                return self::getByID($id);
                            }
                        }
                    }
                }
                return false;
            }

            /**
             * Retrieve a file by UUID
             * @param string $uuid
             * @return bool|\Idno\Common\Entity
             */
            static function getByUUID($uuid) {
                if ($fs = \Idno\Core\site()->db()->getFilesystem()) {
                    return $fs->findOne($uuid);
                }
            }

            /**
             * Retrieve a file by ID
             * @param string $id
             * @return \Idno\Common\Entity|\MongoGridFSFile|null
             */
            static function getByID($id) {
                if ($fs = \Idno\Core\site()->db()->getFilesystem()) {
                    return $fs->findOne(array('_id' => new \MongoId($id)));
                }
            }

            /**
             * Retrieve file data by ID
             * @param string $id
             * @return mixed
             */
            static function getFileDataByID($id) {
                if ($file = self::getByID($id)) {
                    return $file->getBytes();
                }
                return false;
            }

        }

    }