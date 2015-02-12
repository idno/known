<?php

    namespace IdnoPlugins\Photo {

        class Photo extends \Idno\Common\Entity
        {

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
                $sizes                = \Idno\Core\site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => array('large' => 800, 'medium' => 400, 'small' => 200))));
                $eventdata = $sizes->data();
                foreach ($eventdata['sizes'] as $label => $size) {
                    $varname                      = "thumbnail_{$label}";
                    $object['thumbnails'][$label] = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\site()->config()->url, $this->$varname);
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
                    if (!\Idno\Core\site()->triggerEvent("file/upload",[],true)) {
                        return false;
                    }
                }

                $this->title = \Idno\Core\site()->currentPage()->getInput('title');
                $this->body  = \Idno\Core\site()->currentPage()->getInput('body');
                $this->tags  = \Idno\Core\site()->currentPage()->getInput('tags');
                $this->setAccess('PUBLIC');

                if ($time = \Idno\Core\site()->currentPage()->getInput('created')) {
                    if ($time = strtotime($time)) {
                        $this->created = $time;
                    }
                }

                // Get photo
                if ($new) {
                    if (!empty($_FILES['photo']['tmp_name'])) {
                        if (\Idno\Entities\File::isImage($_FILES['photo']['tmp_name'])) {
                            
                            // Extract exif data so we can rotate
                            if (is_callable('exif_read_data') && $_FILES['photo']['type'] == 'image/jpeg') {
                                try {
                                    if ($exif = exif_read_data($_FILES['photo']['tmp_name'])) {
                                        $this->exif = base64_encode(serialize($exif)); // Yes, this is rough, but exif contains binary data that can not be saved in mongo
                                    }
                                } catch (\Exception $e) {
                                    $exif = false;
                                }
                            } else {
                                $exif = false;
                            }
                            
                            if ($photo = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'], true, true)) {
                                $this->attachFile($photo);

                                // Now get some smaller thumbnails, with the option to override sizes
                                $sizes = \Idno\Core\site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => array('large' => 800, 'medium' => 400, 'small' => 200))));
                                $eventdata = $sizes->data();
                                foreach ($eventdata['sizes'] as $label => $size) {

                                    $filename = $_FILES['photo']['name'];

                                    // Experiment: let's not save thumbnails for GIFs, in order to enable animated GIF posting.
                                    if ($_FILES['photo']['type'] != 'image/gif') {
                                        if ($thumbnail = \Idno\Entities\File::createThumbnailFromFile($_FILES['photo']['tmp_name'], "{$filename}_{$label}", $size, false, $exif)) {
                                            $varname        = "thumbnail_{$label}";
                                            $this->$varname = \Idno\Core\site()->config()->url . 'file/' . $thumbnail;

                                            $varname        = "thumbnail_{$label}_id";
                                            $this->$varname = substr($thumbnail, 0, strpos($thumbnail, '/'));
                                        }
                                    }
                                }

                            } else {
                                \Idno\Core\site()->session()->addErrorMessage('Image wasn\'t attached.');
                            }
                        } else {
                            \Idno\Core\site()->session()->addErrorMessage('This doesn\'t seem to be an image ..');
                        }
                    } else {
                        \Idno\Core\site()->session()->addErrorMessage('We couldn\'t access your image. Please try again.');

                        return false;
                    }
                }

                if ($this->save($new)) {
                    \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));

                    return true;
                } else {
                    return false;
                }

            }

        }

    }