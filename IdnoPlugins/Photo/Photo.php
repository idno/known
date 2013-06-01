<?php

    namespace IdnoPlugins\Photo {

        class Photo extends \Idno\Common\Entity {

            function getTitle() {
                if (empty($this->title)) {
                    return 'Untitled';
                } else {
                    return $this->title;
                }
            }

            function getDescription() {
                return $this->body;
            }

            /**
             * Photo objects have type 'image'
             * @return 'image'
             */
            function getActivityStreamsObjectType() {
                return 'image';
            }

            /**
             * Saves changes to this object based on user input
             * @return bool
             */
            function saveDataFromInput() {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $this->title = \Idno\Core\site()->currentPage()->getInput('title');
                $this->body = \Idno\Core\site()->currentPage()->getInput('body');
                $this->setAccess('PUBLIC');

                // Get photo
                if ($new) {
                    if (!empty($_FILES['photo'])) {
                        if ($photo_information = getimagesize($_FILES['photo']['tmp_name'])) {
                            if ($photo = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'],true)) {
                                $this->attachFile($photo);
                                if ($photo_information[0] > 1000 || $photo_information[1] > 1000) {
                                    switch($photo_information['mime']) {
                                        case 'image/jpeg':  $image = imagecreatefromjpeg($_FILES['photo']['tmp_name']); break;
                                        case 'image/png':   $image = imagecreatefrompng($_FILES['photo']['tmp_name']); break;
                                        case 'image/gif':   $image = imagecreatefromgif($_FILES['photo']['tmp_name']); break;
                                    }
                                    if (!empty($image)) {
                                        if ($photo_information[0] > $photo_information[1]) {
                                            $width = 800;
                                            $height = round($photo_information[1] * (800 / $photo_information[0]));
                                        } else {
                                            $height = 600;
                                            $width = round($photo_information[0] * (600 / $photo_information[1]));
                                        }
                                        $image_copy = imagecreatetruecolor($width, $height);
                                        imagecopyresampled($image_copy, $image, 0, 0, 0, 0, $width, $height, $photo_information[0], $photo_information[1]);

                                        if (is_callable('exif_read_data')) {
                                            $exif = exif_read_data($_FILES['photo']['tmp_name']);
                                            if(!empty($exif['Orientation'])) {
                                                switch($exif['Orientation']) {
                                                    case 8:
                                                        $image_copy = imagerotate($image_copy,90,0);
                                                        break;
                                                    case 3:
                                                        $image_copy = imagerotate($image_copy,180,0);
                                                        break;
                                                    case 6:
                                                        $image_copy = imagerotate($image_copy,-90,0);
                                                        break;
                                                }
                                            }
                                        }

                                        $tmp_dir = dirname($_FILES['photo']['tmp_name']);
                                        switch($photo_information['mime']) {
                                            case 'image/jpeg':  imagejpeg($image_copy, $tmp_dir . '/' . $photo->file['_id'] . '.jpg');
                                                                $thumbnail =  \Idno\Core\site()->config()->url . 'file/' . \Idno\Entities\File::createFromFile($tmp_dir . '/' . $photo->file['_id'] . '.jpg', 'thumb.jpg', 'image/jpeg') . '/thumb.jpg';
                                                                @unlink($tmp_dir . '/' . $photo->file['_id'] . '.jpg');
                                                                break;
                                            case 'image/png':   imagepng($image_copy, $tmp_dir . '/' . $photo->file['_id'] . '.png');
                                                                $thumbnail =  \Idno\Core\site()->config()->url . 'file/' . \Idno\Entities\File::createFromFile($tmp_dir . '/' . $photo->file['_id'] . '.png', 'thumb.png', 'image/png') . '/thumb.png';
                                                                @unlink($tmp_dir . '/' . $photo->file['_id'] . '.png');
                                                                break;
                                            case 'image/gif':   imagegif($image_copy, $tmp_dir . '/' . $photo->file['_id'] . '.gif');
                                                                $thumbnail =  \Idno\Core\site()->config()->url . 'file/' . \Idno\Entities\File::createFromFile($tmp_dir . '/' . $photo->file['_id'] . '.gif', 'thumb.gif', 'image/gif') . '/thumb.gif';
                                                                @unlink($tmp_dir . '/' . $photo->file['_id'] . '.gif');
                                                                break;
                                        }
                                    }
                                } else {

                                }

                                $this->thumbnail = $thumbnail;

                            } else {
                                \Idno\Core\site()->session()->addMessage('Image wasn\'t attached.');
                            }
                        } else {
                            \Idno\Core\site()->session()->addMessage('This doesn\'t seem to be an image ..');
                        }
                    }
                }

                if ($this->save()) {
                    if ($new) {
                        $this->addToFeed();
                        \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
                    } // Add it to the Activity Streams feed
                    \Idno\Core\site()->session()->addMessage('Your photo was successfully saved.');
                    return true;
                } else {
                    return false;
                }

            }

        }

    }