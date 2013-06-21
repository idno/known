<?php

    namespace IdnoPlugins\Status {

        class Status extends \Idno\Common\Entity {

            function getTitle() {
                return strip_tags($this->body);
            }

            function getDescription() {
                $body = $this->body;
                if (!empty($this->inreplyto)) {
                    $body = '<a href="'.$this->inreplyto.'" class="u-in-reply-to"></a>' . $body;
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
                $body = \Idno\Core\site()->currentPage()->getInput('body');
                $inreplyto = \Idno\Core\site()->currentPage()->getInput('inreplyto');
                if (!empty($body)) {
                    $this->body = $body;
                    $this->inreplyto = $inreplyto;
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            $this->addToFeed();
                            \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
                        } // Add it to the Activity Streams feed
                        \Idno\Core\site()->session()->addMessage('Your status update was successfully saved.');
                        return true;
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage('You can\'t save an empty status update.');
                }
                return false;

            }

        }

    }