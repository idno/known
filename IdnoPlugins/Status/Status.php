<?php

    namespace IdnoPlugins\Status {

        class Status extends \Idno\Common\Entity {

            function getTitle() {
                return $this->body;
            }

            function getDescription() {
                return $this->body;
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
             * @param \Idno\Common\Page $page
             * @return true|false
             */
            function saveDataFromInput(\Idno\Common\Page $page) {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $body = $page->getInput('body');
                if (!empty($body)) {
                    $this->body = $body;
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) $this->addToFeed(); // Add it to the Activity Streams feed
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