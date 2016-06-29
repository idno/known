<?php

    namespace IdnoPlugins\Media\Pages {

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Media\Media::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Media\Media();
                }

                if ($owner = $object->getOwner()) {
                    $this->setOwner($owner);
                }

                $t = \Idno\Core\Idno::site()->template();
                $edit_body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Media/edit');

                $body = $t->__(['body' => $edit_body])->draw('entity/editwrapper');

                if (empty($object)) {
                    $title = 'Upload audio';
                } else {
                    $title = 'Edit audio details';
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
                    $object = \IdnoPlugins\Media\Media::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Media\Media();
                }

                if ($object->saveDataFromInput()) {
                    $forward = $this->getInput('forward-to', $object->getDisplayURL());
                    $this->forward($forward);
                }

            }

        }

    }