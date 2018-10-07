<?php

namespace IdnoPlugins\Photo {

    use Idno\Entities\File;

    class Photo extends \Idno\Common\Entity
        implements \Idno\Common\JSONLDSerialisable
    {

        // http://php.net/manual/en/features.file-upload.errors.php
        private static $FILE_UPLOAD_ERROR_CODES = array(
                UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success',
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            );

        function getTitle()
        {
            if (empty($this->title)) {
                return \Idno\Core\Idno::site()->language()->_('Untitled');
            } else {
                return $this->title;
            }
        }

        function getDescription()
        {
            return $this->body;
        }

        /**
         * Photo objects have type 'image'
         * @return 'image'
         */
        function getActivityStreamsObjectType()
        {
            return 'image';
        }

        function getMetadataForFeed()
        {
            return array('type' => 'photo');
        }

        /**
         * Extend json serialisable to include some extra data
         */
        public function jsonSerialize()
        {
            $object = parent::jsonSerialize();

            // Add some thumbs
            $object['thumbnails'] = array();
            $sizes = \Idno\Core\Idno::site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event([
                'sizes' => [
                    'large' => 800,
                    'medium' => 400,
                    'small' => 200,
                ]
            ]));
            $eventdata = $sizes->data();
            foreach ($eventdata['sizes'] as $label => $size) {
                $varname                      = "thumbs_{$label}";
                if (!empty($this->$varname) && is_array($this->$varname)) {
                    foreach ($this->$varname as $filename => $data) {
                        $object['thumbnails'][$label][$filename] = [
                            'url' => preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->url, $data['url'])
                        ];
                    }
                }
            }

            return $object;
        }


        /**
         * Saves changes to this object based on user input
         * @return bool
         */
        function saveDataFromInput()
        {

            if (empty($this->_id)) {
                $new = true;
            } else {
                $new = false;
            }

            if ($new) {
                if (!\Idno\Core\Idno::site()->triggerEvent("file/upload", [], true)) {
                    return false;
                }
            }

            $this->title = \Idno\Core\Idno::site()->currentPage()->getInput('title');
            $this->body  = \Idno\Core\Idno::site()->currentPage()->getInput('body');
            $this->tags  = \Idno\Core\Idno::site()->currentPage()->getInput('tags');
            $access = \Idno\Core\Idno::site()->currentPage()->getInput('access');
            $this->setAccess($access);

            if ($time = \Idno\Core\Idno::site()->currentPage()->getInput('created')) {
                if ($time = strtotime($time)) {
                    $this->created = $time;
                }
            }

            // Get photo
            //if ($new) {
                $files = array_filter(\Idno\Core\Input::getFiles('photo'), function($var) {
                    return !empty($var['tmp_name']); // Filter non-filled in elements
                });

                // Replace any existing photos
            //                    if (!empty($files[0]['tmp_name'])) {
            //                        $this->deleteAttachments(); // TODO: Allow edit/removal of existing photos
            //                    }

            foreach ($files as $_file) {

                if (!empty($_file['tmp_name'])) {

                    if (\Idno\Entities\File::isImage($_file['tmp_name']) || \Idno\Entities\File::isSVG($_file['tmp_name'], $_file['name'])) {

                        // Extract exif data so we can rotate
                        if (is_callable('exif_read_data') && $_file['type'] == 'image/jpeg') {
                            try {
                                if (function_exists('exif_read_data')) {
                                    if ($exif = exif_read_data($_file['tmp_name'])) {
                                        $this->exif = base64_encode(serialize($exif)); // Yes, this is rough, but exif contains binary data that cannot be saved in mongo
                                    }
                                }
                            } catch (\Exception $e) {
                                $exif = false;
                            }
                        } else {
                                    $exif = false;

                            if (!is_callable('exif_read_data')) {
                                // Admins get a no-EXIF error
                                if (\Idno\Core\Idno::site()->session()->isAdmin()) {
                                    \Idno\Core\Idno::site()->logging()->log("Because your server doesn't provide EXIF support, Known can't preserve any rotation information in this image.");
                                }
                            }
                        }

                        if ($photo = \Idno\Entities\File::createFromFile($_file['tmp_name'], $_file['name'], $_file['type'], true, true)) {
                            $this->attachFile($photo);

                            // Now get some smaller thumbnails, with the option to override sizes
                            $sizes = \Idno\Core\Idno::site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => array('large' => 800, 'medium' => 400, 'small' => 200))));
                            $eventdata = $sizes->data();
                            foreach ($eventdata['sizes'] as $label => $size) {

                                $filename = $_file['name'];

                                if ($_file['type'] != 'image/gif') {
                                    if ($thumbnail = \Idno\Entities\File::createThumbnailFromFile($_file['tmp_name'], "{$filename}_{$label}", $size, false)) {
                                        // New style thumbnails
                                        $varname        =   "thumbs_{$label}";
                                        if (empty($this->$varname))
                                        $this->$varname = [];

                                        $this->$varname[$filename] = [
                                        'id'    => substr($thumbnail, 0, strpos($thumbnail, '/')),
                                        'url'   => \Idno\Core\Idno::site()->config()->url . 'file/' . $thumbnail,
                                        ];
                                    }
                                }
                            }

                        } else {
                            \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_('Image wasn\'t attached.'));
                            return false;
                        }
                    } else {
                        \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_('This doesn\'t seem to be an image...'));
                        return false;
                    }

                } else {
                    // http://php.net/manual/en/features.file-upload.errors.php
                    $errcode = null;
                    if (!empty($_file['error']))
                        $errcode = $_file['error'];
                    if (!empty($errcode) && !empty(self::$FILE_UPLOAD_ERROR_CODES[intval($errcode)])) {
                        $errmsg = self::$FILE_UPLOAD_ERROR_CODES[intval($errcode)];

                        // No file is ok, if this is not new
                        if (intval($errcode) == UPLOAD_ERR_NO_FILE && !$new) {
                            $errmsg = null;
                        }
                    } else {
                        $errmsg = \Idno\Core\Idno::site()->language()->_('We couldn\'t access your image for an unknown reason. Please try again.');
                    }
                    if (!empty($errmsg)) {
                        \Idno\Core\Idno::site()->session()->addErrorMessage($errmsg);
                        return false;
                    }
                }
            }
            //}

            if ($this->publish($new)) {

                if ($this->getAccess() == 'PUBLIC') {
                    \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
                }

                return true;
            } else {
                return false;
            }

        }

        public function jsonLDSerialise(array $params = array()): array {
            $json = [
                "@context" => "http://schema.org",
                "@type" => 'Photograph',
                'dateCreated' => date('c', $this->getCreatedTime()),
                'datePublished' => date('c', $this->getCreatedTime()),
                'author' => [
                    "@type" => "Person",
                    "name" => $this->getOwner()->getName()
                ],
                'name' => $this->getTitle(),
                'description' => $this->body,
                'url' => $this->getUrl(),
                'mainEntityOfPage' => $this->getUrl(),
            ];
            
            $attachments = $this->getAttachments();
            $attachment = $attachments[0];

            $mainsrc = $attachment['url'];
            $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $mainsrc);
            $mainsrc = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($mainsrc);
            
            $json['image'] = $mainsrc;
            
            return $json;
        }

    }

}
