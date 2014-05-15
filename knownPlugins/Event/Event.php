<?php

    namespace knownPlugins\Event {

        class Event extends \known\Common\Entity {

            function getTitle() {
                if (empty($this->title)) return 'Untitled';
                return $this->title;
            }

            function getDescription() {
                if (!empty($this->body)) return $this->body;
                return '';
            }

            function getURL() {
                if (!($this->getSlug()) && ($this->getID())) {
                    return \known\Core\site()->config()->url . 'event/' . $this->getID() . '/' . $this->getPrettyURLTitle();
                } else {
                    return parent::getURL();
                }
            }

            /**
             * Event objects have type 'article'
             * @return 'article'
             */
            function getActivityStreamsObjectType() {
                return 'event';
            }

            /**
             * Event objects show up as h-event in a Microformats stream
             * @return string
             */
            function getMicroformats2ObjectType() {
                return 'h-event';
            }

            function saveDataFromInput() {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $body = \known\Core\site()->currentPage()->getInput('body');
                if (!empty($body)) {
                    $this->body = $body;
                    $this->title = \known\Core\site()->currentPage()->getInput('title');
                    $this->summary = \known\Core\site()->currentPage()->getInput('summary');
                    $this->location = \known\Core\site()->currentPage()->getInput('location');
                    $this->starttime = \known\Core\site()->currentPage()->getInput('starttime');
                    $this->endtime = \known\Core\site()->currentPage()->getInput('endtime');
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            // Add it to the Activity Streams feed
                            $this->addToFeed();
                        }
                        \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
                        \known\Core\site()->session()->addMessage('Your event was successfully saved.');
                        return true;
                    }
                } else {
                    \known\Core\site()->session()->addMessage('You can\'t save an event with no description.');
                }
                return false;

            }

            function deleteData() {
                \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
            }

        }

    }