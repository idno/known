<?php

    namespace knownPlugins\Event\Pages {

        use known\Pages\Entity\Autosave;

        class Edit extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Event\Event::getByID($this->arguments[0]);
                } else {
                    $object = new \knownPlugins\Event\Event();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Event/edit');

                if (empty($object)) {
                    $title = 'Write an event';
                } else {
                    $title = 'Edit event';
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
                    $object = \knownPlugins\Event\Event::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \knownPlugins\Event\Event();
                }

                if ($object->saveDataFromInput($this)) {
                    (new \known\Core\Autosave())->clearContext('event');
                    $this->forward($object->getURL());
                }

            }

        }

    }