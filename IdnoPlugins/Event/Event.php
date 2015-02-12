<?php

    namespace IdnoPlugins\Event {

        class Event extends \Idno\Common\Entity {

            function getTitle() {
                if (empty($this->title)) return 'Untitled';
                return $this->title;
            }

            function getDescription() {
                if (!empty($this->body)) return $this->body;
                return '';
            }

            function getURL() {
                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }
                if (!($this->getSlug()) && ($this->getID())) {
                    return \Idno\Core\site()->config()->url . 'event/' . $this->getID() . '/' . $this->getPrettyURLTitle();
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
                $body = \Idno\Core\site()->currentPage()->getInput('body');
                if (!empty($body)) {
                    $this->body = $body;
                    $this->title = \Idno\Core\site()->currentPage()->getInput('title');
                    $this->summary = \Idno\Core\site()->currentPage()->getInput('summary');
                    $this->location = \Idno\Core\site()->currentPage()->getInput('location');
                    $this->starttime = \Idno\Core\site()->currentPage()->getInput('starttime');
                    $this->endtime = \Idno\Core\site()->currentPage()->getInput('endtime');

                    if ($time = \Idno\Core\site()->currentPage()->getInput('created')) {
                        if ($time = strtotime($time)) {
                            $this->created = $time;
                        }
                    }

                    $this->setAccess('PUBLIC');
                    if ($this->save($new)) {
                        \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
                        return true;
                    }
                } else {
                    \Idno\Core\site()->session()->addErrorMessage('You can\'t save an event with no description.');
                }
                return false;

            }

            function deleteData() {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
            }

        }

    }