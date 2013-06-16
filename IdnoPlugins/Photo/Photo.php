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
                    if (!empty($_FILES['photo']['tmp_name'])) {
                        if (\Idno\Entities\File::isImage($_FILES['photo']['tmp_name'])) {
                            if ($photo = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'],true)) {
                                $this->attachFile($photo);
                                if ($thumbnail = \Idno\Entities\File::createThumbnailFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'])) {
                                    $this->thumbnail = $thumbnail;
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