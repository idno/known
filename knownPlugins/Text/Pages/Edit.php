<?php

    namespace knownPlugins\Text\Pages {

        use known\Core\Autosave;

        class Edit extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Text\Entry::getByID($this->arguments[0]);
                } else {
                    $object = new \knownPlugins\Text\Entry();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Entry/edit');

                if (empty($object)) {
                    $title = 'Write an entry';
                } else {
                    $title = 'Edit entry';
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
                    $object = \knownPlugins\Text\Entry::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \knownPlugins\Text\Entry();
                }

                if ($object->saveDataFromInput($this)) {
                    (new \known\Core\Autosave())->clearContext('entry');
                    $this->forward($object->getURL());
                }

            }

        }

    }