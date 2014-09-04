<?php

    namespace IdnoPlugins\Text {

        class Entry extends \Idno\Common\Entity {

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

                if (!$this->getSlug() && ($this->getID())) {
                    return \Idno\Core\site()->config()->url . 'entry/' . $this->getID() . '/' . $this->getPrettyURLTitle();
                } else {
                    return parent::getURL();
                }

            }

            /**
             * Entry objects have type 'article'
             * @return 'article'
             */
            function getActivityStreamsObjectType() {
                return 'article';
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
                    $this->tags = \Idno\Core\site()->currentPage()->getInput('tags');
                    $this->setAccess('PUBLIC');

                    if ($time = \Idno\Core\site()->currentPage()->getInput('created')) {
                        if ($time = strtotime($time)) {
                            $this->created = $time;
                        }
                    }

                    if ($this->save()) {
                        if ($new) {
                            // Add it to the Activity Streams feed
                            $this->addToFeed();
                        }
                        \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
                        return true;
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage('You can\'t save an empty entry.');
                }
                return false;

            }

            function deleteData() {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
            }

        }

    }