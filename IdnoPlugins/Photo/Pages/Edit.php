<?php

    namespace IdnoPlugins\Photo\Pages {

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Photo\Photo::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Photo\Photo();
                }

                $t = \Idno\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Photo/edit');

                if (empty($object)) {
                    $title = 'Upload a picture';
                } else {
                    $title = 'Edit picture details';
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
                    $object = \IdnoPlugins\Photo\Photo::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Photo\Photo();
                }

                if ($object->saveDataFromInput($this)) {
                    //$this->forward(\Idno\Core\site()->config()->getURL() . 'content/all/#feed');
                    $this->forward($object->getURL());
                }

            }

        }

    }