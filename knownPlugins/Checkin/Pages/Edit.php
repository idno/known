<?php

    namespace knownPlugins\Checkin\Pages {

        class Edit extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Checkin\Checkin::getByID($this->arguments[0]);
                } else {
                    $object = new \knownPlugins\Checkin\Checkin();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Checkin/edit');

                if (empty($object)) {
                    $title = 'Where are you?';
                } else {
                    $title = 'Edit checkin';
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
                    $object = \knownPlugins\Checkin\Checkin::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \knownPlugins\Checkin\Checkin();
                }

                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }

            }

        }

    }