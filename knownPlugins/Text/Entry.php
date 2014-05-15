<?php

    namespace knownPlugins\Text {

        class Entry extends \known\Common\Entity {

            function getTitle() {
                if (empty($this->title)) return 'Untitled';
                return $this->title;
            }

            function getDescription() {
                if (!empty($this->body)) return $this->body;
                return '';
            }

            function getURL() {
                if (!$this->getSlug() && ($this->getID())) {
                    return \known\Core\site()->config()->url . 'entry/' . $this->getID() . '/' . $this->getPrettyURLTitle();
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
                $body = \known\Core\site()->currentPage()->getInput('body');
                if (!empty($body)) {
                    $this->body = $body;
                    $this->title = \known\Core\site()->currentPage()->getInput('title');
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            // Add it to the Activity Streams feed
                            $this->addToFeed();
                        }
                        \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
                        \known\Core\site()->session()->addMessage('Your entry was successfully saved.');
                        return true;
                    }
                } else {
                    \known\Core\site()->session()->addMessage('You can\'t save an empty entry.');
                }
                return false;

            }

            function deleteData() {
                \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
            }

        }

    }