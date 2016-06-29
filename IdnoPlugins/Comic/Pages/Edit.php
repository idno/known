<?php

    namespace IdnoPlugins\Comic\Pages {

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Comic\Comic::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Comic\Comic();
                }

                if ($owner = $object->getOwner()) {
                    $this->setOwner($owner);
                }

                $t = \Idno\Core\Idno::site()->template();
                $edit_body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Comic/edit');

                $body = $t->__(['body' => $edit_body])->draw('entity/editwrapper');

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
                $this->createGatekeeper();

                $new = false;
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Comic\Comic::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Comic\Comic();
                }

                if ($object->saveDataFromInput()) {
                    $forward = $this->getInput('forward-to', $object->getDisplayURL());
                    $this->forward($forward);
                }

            }

        }

    }