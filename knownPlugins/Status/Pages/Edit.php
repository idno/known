<?php

    namespace knownPlugins\Status\Pages {

        class Edit extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Status\Status::getByID($this->arguments[0]);
                } else {
                    $object = new \knownPlugins\Status\Status();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object,
                    'url' => $this->getInput('url'),
                    'body' => $this->getInput('body')
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

                $new = false;
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Status\Status::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \knownPlugins\Status\Status();
                }

                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }

            }

        }

    }