<?php

    namespace knownPlugins\Like\Pages {

        class Edit extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Like\Like::getByID($this->arguments[0]);
                } else {
                    $object = new \knownPlugins\Like\Like();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Like/edit');

                if (empty($object)) {
                    $title = 'What are you up to?';
                } else {
                    $title = 'Edit like update';
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
                    $object = \knownPlugins\Like\Like::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \knownPlugins\Like\Like();
                }

                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }

            }

        }

    }