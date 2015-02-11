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
            function getMimeType()
            {
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
            function getURL()
            {
                if (!empty($this->_id)) {
                    return \Idno\Core\site()->config()->url . 'file/' . $this->_id . '/' . urlencode($this->filename);
                }

                return '';
            }

            function delete()
            {
                // TODO deleting files would be good ...
            }

            function remove()
            {
                return $this->delete();
            }

            /**
             * Passes through the contents of this file.
             */
            function passThroughBytes()
            {
                echo $this->getBytes();
            }

            /**
             * Save a file to the filesystem and return the ID
             *
             * @param string $file_path Full local path to the file
             * @param string $filename Filename to store
             * @param string $mime_type MIME type associated with the file
             * @param bool $return_object Return the file object? If set to false (as is default), will return the ID
             * @param bool $destroy_exif When true, if an image is uploaded the exif data will be destroyed.
             * @return bool|\id Depending on success
             */
            public static function createFromFile($file_path, $filename, $mime_type = 'application/octet-stream', $return_object = false, $destroy_exif = false)
            {
                if (file_exists($file_path) && !empty($filename)) {
                    if ($fs = \Idno\Core\site()->filesystem()) {
                        $file     = new File();
                        $metadata = array(
                            'filename'  => $filename,
                            'mime_type' => $mime_type
                        );

                        // Get image filesize
                        if (self::isImage($file_path)) {
                            $photo_information = getimagesize($file_path);
                            if (!empty($photo_information[0]) && !empty($photo_information[1])) {
                                $metadata['width'] = $photo_information[0];
                                $metadata['height'] = $photo_information[1];
                            }
                        }

                        // Do we want to remove EXIF data?
                        if (!empty($photo_information) && $destroy_exif)
                        {
                            $tmpfname = $file_path;
                            switch ($photo_information['mime']) {
                                case 'image/jpeg':
                                    $image = imagecreatefromjpeg($file_path);
                                    imagejpeg($image, $tmpfname);
                                    break;
                            }
                            
                        }
                        
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
             * Determines whether a file is an image or not.
             * @param string $file_path The path to a file
             * @return bool
             */
            public static function isImage($file_path)
            {
                if ($photo_information = getimagesize($file_path)) {
                    return true;
                }

                return false;
            }

            /**
             * Given a path to an image on disk, generates and saves a thumbnail with maximum dimension $max_dimension.
             * @param string $file_path Path to the file.
             * @param string $filename Filename that the file should have on download.
             * @param int $max_dimension The maximum number of pixels the thumbnail image should be along its longest side.
             * @param bool $square If this is set to true, the thumbnail will be made square.
             * @param mixed $exif Optionally provide exif data for the image, if not provided then this function will attempt to extract it
             * @return bool|id
             */
            public static function createThumbnailFromFile($file_path, $filename, $max_dimension = 800, $square = false, $exif = null)
            {

                $thumbnail = false;

                if ($photo_information = getimagesize($file_path)) {
                    if ($photo_information[0] > $max_dimension || $photo_information[1] > $max_dimension) {
                        switch ($photo_information['mime']) {
                            case 'image/jpeg':
                                $image = imagecreatefromjpeg($file_path);
                                break;
                            case 'image/png':
                                $image      = imagecreatefrompng($file_path);
                                $background = imagecolorallocatealpha($image, 0, 0, 0, 127);
                                imagecolortransparent($image, $background);
                                break;
                            case 'image/gif':
                                $image      = imagecreatefromgif($file_path);
                                $background = imagecolorallocatealpha($image, 0, 0, 0, 127);
                                imagecolortransparent($image, $background);
                                break;
                        }
                        if (!empty($image)) {
                            if ($photo_information[0] > $photo_information[1]) {
                                $width  = $max_dimension;
                                $height = round($photo_information[1] * ($max_dimension / $photo_information[0]));
                            } else {
                                $height = $max_dimension;
                                $width  = round($photo_information[0] * ($max_dimension / $photo_information[1]));
                            }
                            if ($square) {
                                if ($width > $height) {
                                    $new_height = $max_dimension;
                                    $new_width = $max_dimension;
                                    $original_height = $photo_information[1];
                                    $original_width = $photo_information[1];
                                    $offset_x = round(($photo_information[0] - $photo_information[1]) / 2);
                                    $offset_y = 0;
                                } else {
                                    $new_height = $max_dimension;
                                    $new_width = $max_dimension;
                                    $original_height = $photo_information[0];
                                    $original_width = $photo_information[0];
                                    $offset_x = 0;
                                    $offset_y = round(($photo_information[1] - $photo_information[0]) / 2);
                                }
                            } else {
                                $new_height = $height;
                                $new_width = $width;
                                $original_height = $photo_information[1];
                                $original_width = $photo_information[0];
                                $offset_x = 0;
                                $offset_y = 0;
                            }
                            $image_copy = imagecreatetruecolor($new_width, $new_height);
                            imagealphablending($image_copy, false);
                            imagesavealpha($image_copy, true);
                            imagecopyresampled($image_copy, $image, 0, 0, $offset_x, $offset_y, $new_width, $new_height, $original_width, $original_height);


                            if (is_callable('exif_read_data') && $photo_information['mime'] == 'image/jpeg') {
                                try {
                                    if (!$exif)
                                        $exif = exif_read_data($file_path);
                                    if (!empty($exif['Orientation'])) {
                                        switch ($exif['Orientation']) {
                                            case 8:
                                                $image_copy = imagerotate($image_copy, 90, 0);
                                                break;
                                            case 3:
                                                $image_copy = imagerotate($image_copy, 180, 0);
                                                break;
                                            case 6:
                                                $image_copy = imagerotate($image_copy, -90, 0);
                                                break;
                                        }
                                    }
                                } catch (\Exception $e) {
                                    // Don't do anything
                                }
                            }
                            

                            $tmp_dir = dirname($file_path);
                            switch ($photo_information['mime']) {
                                case 'image/jpeg':
                                    imagejpeg($image_copy, $tmp_dir . '/' . $filename . '.jpg');
                                    $thumbnail = \Idno\Entities\File::createFromFile($tmp_dir . '/' . $filename . '.jpg', "thumb_{$max_dimension}.jpg", 'image/jpeg') . '/thumb.jpg';
                                    @unlink($tmp_dir . '/' . $filename . '.jpg');
                                    break;
                                case 'image/png':
                                    imagepng($image_copy, $tmp_dir . '/' . $filename . '.png');
                                    $thumbnail = \Idno\Entities\File::createFromFile($tmp_dir . '/' . $filename . '.png', "thumb_{$max_dimension}.png", 'image/png') . '/thumb.png';
                                    @unlink($tmp_dir . '/' . $filename . '.png');
                                    break;
                                case 'image/gif':
                                    imagegif($image_copy, $tmp_dir . '/' . $filename . '.gif');
                                    $thumbnail = \Idno\Entities\File::createFromFile($tmp_dir . '/' . $filename . '.gif', "thumb_{$max_dimension}.gif", 'image/gif') . '/thumb.gif';
                                    @unlink($tmp_dir . '/' . $filename . '.gif');
                                    break;
                            }
                        }
                    } else {

                    }

                    return $thumbnail;

                }

                return false;
            }

            /**
             * Retrieve a file by UUID
             * @param string $uuid
             * @return bool|\Idno\Common\Entity
             */
            static function getByUUID($uuid)
            {
                if ($fs = \Idno\Core\site()->filesystem()) {
                    return $fs->findOne($uuid);
                }

                return false;
            }

            /**
             * Retrieve a file by ID
             * @param string $id
             * @return \Idno\Common\Entity|\MongoGridFSFile|null
             */
            static function getByID($id)
            {
                if ($fs = \Idno\Core\site()->filesystem()) {
                    try {
                        return $fs->findOne(array('_id' => \Idno\Core\site()->db()->processID($id)));
                    } catch (\Exception $e) {
                        \Idno\Core\site()->logging->log($e->getMessage(), LOGLEVEL_ERROR);
                    }
                }

                return false;
            }

            /**
             * Attempt to extract a file from a URL to it. Will fail with false if the file is external or otherwise
             * can't be retrieved.
             * @param $url
             * @return \Idno\Common\Entity|\MongoGridFSFile|null
             */
            static function getByURL($url)
            {
                if (substr_count($url, \Idno\Core\site()->config()->getDisplayURL() . 'file/')) {
                    $url = str_replace(\Idno\Core\site()->config()->getDisplayURL() . 'file/','',$url);
                    return self::getByID($url);
                }
                return false;
            }

            /**
             * Retrieve file data by ID
             * @param string $id
             * @return mixed
             */
            static function getFileDataByID($id)
            {
                if ($file = self::getByID($id)) {
                    try {
                        return $file->getBytes();
                    } catch (\Exception $e) {
                        \Idno\Core\site()->logging->log($e->getMessage(), LOGLEVEL_ERROR);
                    }
                }

                return false;
            }

            /**
             * Retrieve file data from an attachment (first trying load from local storage, then from URL)
             * @param $attachment
             * @return bool|mixed|string
             */
            static function getFileDataFromAttachment($attachment) {
                \Idno\Core\site()->logging->log(json_encode($attachment), LOGLEVEL_DEBUG);
                if (!empty($attachment['_id'])) {
                    //\Idno\Core\site()->logging->log("Checking attachment ID", LOGLEVEL_DEBUG);
                    if ($bytes = self::getFileDataByID((string)$attachment['_id'])) {
                        //\Idno\Core\site()->logging->log("Retrieved some bytes", LOGLEVEL_DEBUG);
                        if (strlen($bytes)) {
                            //\Idno\Core\site()->logging->log("Bytes! " . $bytes, LOGLEVEL_DEBUG);
                            return $bytes;
                        } else {
                            //\Idno\Core\site()->logging->log("Sadly no bytes", LOGLEVEL_DEBUG);
                        }
                    } else {
                        //\Idno\Core\site()->logging->log("No bytes retrieved", LOGLEVEL_DEBUG);
                    }
                } else {
                    \Idno\Core\site()->logging->log("Empty attachment _id", LOGLEVEL_DEBUG);
                }
                if (!empty($attachment['url'])) {
                    try {
                        if ($bytes = @file_get_contents($attachment['url'])) {
                            \Idno\Core\site()->logging->log("Returning bytes", LOGLEVEL_DEBUG);
                            return $bytes;
                        } else {
                            \Idno\Core\site()->logging->log("Couldn't get bytes from " . $attachment['url'], LOGLEVEL_DEBUG);
                        }
                    } catch (\Exception $e) {
                        \Idno\Core\site()->logging->log("Couldn't get bytes from " . $attachment['url'], LOGLEVEL_DEBUG);
                    }
                } else {
                    \Idno\Core\site()->logging->log('Attachment url was empty ' . $attachment['url'], LOGLEVEL_DEBUG);
                }

                return false;
            }

        }

    }