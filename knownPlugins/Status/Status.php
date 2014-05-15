<?php

    namespace knownPlugins\Status {

        class Status extends \known\Common\Entity {

            function getTitle() {
                return strip_tags($this->body);
            }

            function getDescription() {
                $body = $this->body;
                if (!empty($this->inreplyto)) {
                    if (is_array($this->inreplyto)) {
                        foreach($this->inreplyto as $inreplyto) {
                            $body = '<a href="'.$inreplyto.'" class="u-in-reply-to"></a>' . $body;
                        }
                    } else {
                        $body = '<a href="'.$this->inreplyto.'" class="u-in-reply-to"></a>' . $body;
                    }
                }
                if (!empty($this->syndicatedto)) {
                    foreach($this->syndicatedto as $syndicated) {
                        $body = '<a href="'.$syndicated.'" class="u-in-reply-to"></a>' . $body;
                    }
                }
                return $body;
            }

            /**
             * Status objects have type 'note'
             * @return 'note'
             */
            function getActivityStreamsObjectType() {
                return 'note';
            }

            /**
             * Saves changes to this object based on user input
             * @return true|false
             */
            function saveDataFromInput() {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $body = \known\Core\site()->currentPage()->getInput('body');
                $inreplyto = \known\Core\site()->currentPage()->getInput('inreplyto');
                if (!empty($body)) {
                    $this->body = $body;
                    $this->inreplyto = $inreplyto;
                    if (!empty($inreplyto)) {
                        if (is_array($inreplyto)) {
                            foreach($inreplyto as $inreplytourl) {
                                $this->syndicatedto = \known\Core\Webmention::addSyndicatedReplyTargets($inreplytourl, $this->syndicatedto);
                            }
                        } else {
                            $this->syndicatedto = \known\Core\Webmention::addSyndicatedReplyTargets($inreplyto);
                        }
                    }
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            $this->addToFeed();
                        } // Add it to the Activity Streams feed
                        \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
                        \known\Core\site()->session()->addMessage('Your status update was successfully saved.');
                        return true;
                    }
                } else {
                    \known\Core\site()->session()->addMessage('You can\'t save an empty status update.');
                }
                return false;

            }

            function deleteData() {
                \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
            }

        }

    }