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
                $object['thumbnails'] = [];
                $sizes                = \Idno\Core\site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => ['large' => 800, 'medium' => 400, 'small' => 200])));
                foreach ($sizes->data()['sizes'] as $label => $size) {
                    $varname                      = "thumbnail_{$label}";
                    $object['thumbnails'][$label] = $this->$varname;
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
                            if (is_callable('exif_read_data')) {
                                $exif = exif_read_data($_FILES['photo']['tmp_name']);
                                $this->exif = base64_encode(serialize($exif)); // Yes, this is rough, but exif contains binary data that can not be saved in mongo
                            } else {
                                $exif = false;
                            }
                            
                            if ($photo = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'], true, true)) {
                                $this->attachFile($photo);

                                // Now get some smaller thumbnails, with the option to override sizes
                                $sizes = \Idno\Core\site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => ['large' => 800, 'medium' => 400, 'small' => 200])));
                                foreach ($sizes->data()['sizes'] as $label => $size) {

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
                                \Idno\Core\site()->session()->addMessage('Image wasn\'t attached.');
                            }
                        } else {
                            \Idno\Core\site()->session()->addMessage('This doesn\'t seem to be an image ..');
                        }
                    } else {
                        \Idno\Core\site()->session()->addMessage('We couldn\'t access your image. Please try again.');

                        return false;
                    }
                }

                if ($this->save()) {
                    if ($new) {
                        $this->addToFeed();
                    } // Add it to the Activity Streams feed
                    \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));

                    return true;
                } else {
                    return false;
                }

            }

        }

    }