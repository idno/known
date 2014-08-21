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
             * @return bool|\id Depending on success
             */
            public static function createFromFile($file_path, $filename, $mime_type = 'application/octet-stream', $return_object = false)
            {
                if (file_exists($file_path) && !empty($filename)) {
                    if ($fs = \Idno\Core\site()->filesystem()) {
                        $file     = new File();
                        $metadata = array(
                            'filename'  => $filename,
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
             * @return bool|id
             */
            public static function createThumbnailFromFile($file_path, $filename, $max_dimension = 800)
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
                                $background = imagecolorallocate($image, 0, 0, 0);
                                imagecolortransparent($image, $background);
                                imagealphablending($image, false);
                                imagesavealpha($image, true);
                                break;
                            case 'image/gif':
                                $image      = imagecreatefromgif($file_path);
                                $background = imagecolorallocate($image, 0, 0, 0);
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
                            $image_copy = imagecreatetruecolor($width, $height);
                            imagecopyresampled($image_copy, $image, 0, 0, 0, 0, $width, $height, $photo_information[0], $photo_information[1]);

                            if (is_callable('exif_read_data')) {
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
                        error_log($e->getMessage());
                    }
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
                        error_log($e->getMessage());
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
                error_log(json_encode($attachment));
                if (!empty($attachment['_id'])) {
                    error_log("Checking attachment ID");
                    if ($bytes = self::getFileDataByID((string)$attachment['_id'])) {
                        error_log("Retrieved some bytes");
                        if (strlen($bytes)) {
                            error_log("Bytes! " . $bytes);
                            return $bytes;
                        } else {
                            error_log("Sadly no bytes");
                        }
                    } else {
                        error_log("No bytes retrieved");
                    }
                } else {
                    error_log("Empty attachment _id");
                }
                if (!empty($attachment['url'])) {
                    if ($bytes = file_get_contents($attachment['url'])) {
                        error_log("Returning bytes");
                        return $bytes;
                    } else {
                        error_log("Couldn't get bytes from " . $attachment['url']);
                    }
                } else {
                    error_log('Attachment url was empty ' . $attachment['url']);
                }

                return false;
            }

        }

    }