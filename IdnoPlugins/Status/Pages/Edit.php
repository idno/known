<?php

    namespace IdnoPlugins\Status\Pages {

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Status\Status::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Status\Status();
                }

                $t = \Idno\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object,
                    'url' => $this->getInput('url'),
                    'body' => $this->getInput('body'),
                    'tags' => $this->getInput('tags')
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
                $this->createGatekeeper();

                $new = false;
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Status\Status::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Status\Status();
                }

                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }

            }

        }

    }