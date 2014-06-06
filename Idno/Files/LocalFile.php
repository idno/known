<?php

    namespace Idno\Files {

        class LocalFile extends File {

            public $internal_filename = '';
            public $metadata_filename = '';

            /**
             * Get this file's contents
             * @return mixed|string
             */
            function getBytes() {
                if (file_exists($this->internal_filename)) {
                    return file_get_contents($this->internal_filename);
                }
            }

            /**
             * Delete this file
             */
            function delete() {
                @unlink($this->internal_filename);
                @unlink($this->metadata_filename);
            }

            /**
             * Writes this file to the filename specified in $path
             * @param string $path
             * @return bool|mixed
             */
            function write($path) {
                return @copy($this->internal_filename, $path);
            }

            /**
             * Returns this file's filename
             * @return string
             */
            function getFilename() {
                if (!empty($this->metadata['filename'])) {
                    return $this->metadata['filename'];
                }
                return basename($this->internal_filename);
            }

        }

    }