<?php

    namespace IdnoPlugins\Like\Pages {

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Like\Like::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Like\Like();
                }

                $t = \Idno\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object,
                    'url' => $this->getInput('url')
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
                $this->createGatekeeper();

                $new = false;
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Like\Like::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Like\Like();
                }

                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }

            }

        }

    }