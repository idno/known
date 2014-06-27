<?php

    namespace IdnoPlugins\Media {

        class Media extends \Idno\Common\Entity {

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
             * Media objects have type 'media'
             * @return 'media'
             */
            function getActivityStreamsObjectType() {
                return 'media';
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

                // Get media
                if ($new) {
                    if (!empty($_FILES['media']['tmp_name'])) {
                        if (in_array($_FILES['media']['type'],
                            [
                                'video/mp4',
                                'video/mov',
                                'video/webm',
                                'video/ogg',
                                'audio/mp4',
                                'audio/mpeg',
                                'audio/mp3',
                                'audio/ogg',
                                'audio/vorbis'
                            ]
                        )) {
                            if ($media = \Idno\Entities\File::createFromFile($_FILES['media']['tmp_name'], $_FILES['media']['name'], $_FILES['media']['type'],true)) {
                                $this->attachFile($media);
                            } else {
                                \Idno\Core\site()->session()->addMessage('Media wasn\'t attached.');
                            }
                        } else {
                            \Idno\Core\site()->session()->addMessage('This doesn\'t seem to be a media file ..');
                        }
                    } else {
                        \Idno\Core\site()->session()->addMessage('We couldn\'t access your media. Please try again.');
                        return false;
                    }
                }

                $this->media_type = $_FILES['media']['type'];

                if ($this->save()) {
                    if ($new) {
                        $this->addToFeed();
                    } // Add it to the Activity Streams feed
                    \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
                    return true;
                } else {
                    return false;
                }

            }

        }

    }