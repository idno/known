<?php

    namespace IdnoPlugins\Photo {

        use Idno\Entities\File;

        class Photo extends \Idno\Common\Entity
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
                    return 'Untitled';
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

            /**
             * Extend json serialisable to include some extra data
             */
            public function jsonSerialize()
            {
                $object = parent::jsonSerialize();

                // Add some thumbs
                $object['thumbnails'] = array();
                $sizes                = \Idno\Core\Idno::site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => array('large' => 800, 'medium' => 400, 'small' => 200))));
                $eventdata = $sizes->data();
                foreach ($eventdata['sizes'] as $label => $size) {
                    $varname                      = "thumbnail_{$label}";
                    $object['thumbnails'][$label] = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->url, $this->$varname);
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
                    if (!\Idno\Core\Idno::site()->triggerEvent("file/upload",[],true)) {
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
                if ($new) {
                    if (!empty($_FILES['photo']['tmp_name'])) {
                        if (\Idno\Entities\File::isImage($_FILES['photo']['tmp_name']) || \Idno\Entities\File::isSVG($_FILES['photo']['tmp_name'], $_FILES['photo']['name'])) {

                            // Extract exif data so we can rotate
                            if (is_callable('exif_read_data') && $_FILES['photo']['type'] == 'image/jpeg') {
                                try {
                                    if (function_exists('exif_read_data')) {
                                        if ($exif = exif_read_data($_FILES['photo']['tmp_name'])) {
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
                                        //\Idno\Core\Idno::site()->session()->addErrorMessage("Because your server doesn't provide EXIF support, Known can't preserve any rotation information in this image.");
                                    }
                                }
                            }

                            if ($photo = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'], true, true)) {
                                $this->attachFile($photo);

                                // Now get some smaller thumbnails, with the option to override sizes
                                $sizes = \Idno\Core\Idno::site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => array('large' => 800, 'medium' => 400, 'small' => 200))));
                                $eventdata = $sizes->data();
                                foreach ($eventdata['sizes'] as $label => $size) {

                                    $filename = $_FILES['photo']['name'];

                                    // Experiment: let's not save thumbnails for GIFs, in order to enable animated GIF posting.
                                    if ($_FILES['photo']['type'] != 'image/gif') {
	                                    if ($thumbnail = \Idno\Entities\File::createThumbnailFromFile($_FILES['photo']['tmp_name'], "{$filename}_{$label}", $size, false)) {
                                            $varname        = "thumbnail_{$label}";
                                            $this->$varname = \Idno\Core\Idno::site()->config()->url . 'file/' . $thumbnail;

                                            $varname        = "thumbnail_{$label}_id";
                                            $this->$varname = substr($thumbnail, 0, strpos($thumbnail, '/'));
                                        }
                                    }
                                }

                            } else {
                                \Idno\Core\Idno::site()->session()->addErrorMessage('Image wasn\'t attached.');
                                return false;
                            }
                        } else {
                            \Idno\Core\Idno::site()->session()->addErrorMessage('This doesn\'t seem to be an image ..');
                            return false;
                        }
                    } else {
	                    // http://php.net/manual/en/features.file-upload.errors.php
	                    $errcode = $_FILES['photo']['error'];
	                    if (!empty($errcode) && !empty(self::$FILE_UPLOAD_ERROR_CODES[intval($errcode)])) {
		                    $errmsg = self::$FILE_UPLOAD_ERROR_CODES[intval($errcode)];
	                    } else {
		                    $errmsg = 'We couldn\'t access your image for an unknown reason. Please try again.';
	                    }
	                    \Idno\Core\Idno::site()->session()->addErrorMessage($errmsg);
                        return false;
                    }
                }

                if ($this->publish($new)) {

                    if ($this->getAccess() == 'PUBLIC') {
                        \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
                    }

                    return true;
                } else {
                    return false;
                }

            }

        }

    }