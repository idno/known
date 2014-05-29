<?php

    namespace IdnoPlugins\Video {

        class Video extends \Idno\Common\Entity {

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
             * Video objects have type 'video'
             * @return 'video'
             */
            function getActivityStreamsObjectType() {
                return 'video';
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

                // Get video
                if ($new) {
                    if (!empty($_FILES['video']['tmp_name'])) {
                        if (/*\Idno\Entities\File::isVideo($_FILES['video']['tmp_name'])*/true) {
                            if ($video = \Idno\Entities\File::createFromFile($_FILES['video']['tmp_name'], $_FILES['video']['name'], $_FILES['video']['type'],true)) {
                                $this->attachFile($video);
                            } else {
                                \Idno\Core\site()->session()->addMessage('Video wasn\'t attached.');
                            }
                        } else {
                            \Idno\Core\site()->session()->addMessage('This doesn\'t seem to be a video ..');
                        }
                    } else {
                        \Idno\Core\site()->session()->addMessage('We couldn\'t access your video. Please try again.');
                        return false;
                    }
                }

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