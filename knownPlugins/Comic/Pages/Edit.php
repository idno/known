<?php

    namespace knownPlugins\Comic\Pages {

        class Edit extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Comic\Comic::getByID($this->arguments[0]);
                } else {
                    $object = new \knownPlugins\Comic\Comic();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Comic/edit');

                if (empty($object)) {
                    $title = 'Post a comic';
                } else {
                    $title = 'Edit comic description';
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
                    $object = \knownPlugins\Comic\Comic::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \knownPlugins\Comic\Comic();
                }

                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }

            }

        }

    }