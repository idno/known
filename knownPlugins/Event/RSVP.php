<?php

    namespace knownPlugins\Event {

        class RSVP extends \known\Common\Entity {

            function getTitle() {
                if (!empty($this->body)) return $this->body;
                return '';
            }

            function getDescription() {
                $body = $this->body;
                if (!empty($this->inreplyto)) {
                    $body = '<a href="'.$this->inreplyto.'" class="u-in-reply-to"></a>' . $body;
                }
                if (!empty($this->rsvp)) {
                    $body = '<data class="p-rsvp" value="'.$this->rsvp.'">' . $body . '</data>';
                }
                return $body;
            }

            function getURL() {
                if (!($this->getSlug()) && ($this->getID())) {
                    return \known\Core\site()->config()->url . 'rsvp/' . $this->getID() . '/';
                } else {
                    return parent::getURL();
                }
            }

            /**
             * Event objects have type 'article'
             * @return 'article'
             */
            function getActivityStreamsObjectType() {
                return 'note';
            }

            /**
             * Event objects show up as h-event in a Microformats stream
             * @return string
             */
            function getMicroformats2ObjectType() {
                return 'h-entry';
            }

            function saveDataFromInput() {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $body = \known\Core\site()->currentPage()->getInput('body');
                $rsvp = \known\Core\site()->currentPage()->getInput('rsvp');
                if (!empty($rsvp)) {
                    $this->body = $body;
                    $rsvp = strtolower($rsvp);
                    if ($rsvp != 'yes' && $rsvp != 'maybe') {
                        $rsvp = 'no';
                    }
                    $this->rsvp = $rsvp;
                    $this->inreplyto = \known\Core\site()->currentPage()->getInput('inreplyto');
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            // Add it to the Activity Streams feed
                            $this->addToFeed();
                        }
                        \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
                        \known\Core\site()->session()->addMessage('Your RSVP was successfully saved.');
                        return true;
                    }
                } else {
                    \known\Core\site()->session()->addMessage('You can\'t save an RSVP with no status.');
                }
                return false;

            }

            function deleteData() {
                \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
            }

        }

    }