<?php

    namespace IdnoPlugins\Status\Pages {

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Status\Status::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Status\Status();
                }

                $t = \Idno\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Status/edit');

                if (empty($object)) {
                    $title = 'What are you up to?';
                } else {
                    $title = 'Edit status update';
                }

                if (!empty($this->xhr)) {
                    echo $body;
                } else {
                    $t->__(array('body' => $body, 'title' => $title))->drawPage();
                }
            }

            function postContent() {
                $this->gatekeeper();

                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Status\Status::getByID($this->arguments[0]);
                }
                if (empty($object)) $object = new \IdnoPlugins\Status\Status();

                $body = $this->getInput('body');
                if (!empty($body)) {
                    $object->body = $body;
                    $object->setAccess('PUBLIC');
                    if ($object->save()) {
                        \Idno\Core\site()->session()->addMessage('Your status update was successfully saved.');
                        $this->forward($object->getURL());
                    } else {
                        \Idno\Core\site()->session()->addMessage('We couldn\'t save your status update.');
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage('You can\'t save an empty status update.');
                }

            }

        }

    }