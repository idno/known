<?php

    namespace knownPlugins\Event\Pages\RSVP {

        class Edit extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Event\RSVP::getByID($this->arguments[0]);
                } else {
                    $object = new \knownPlugins\Event\RSVP();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object,
                    'url' => $this->getInput('url')
                ))->draw('entity/RSVP/edit');

                if (empty($object)) {
                    $title = 'Write an RSVP';
                } else {
                    $title = 'Edit RSVP';
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
                    $object = \knownPlugins\Event\RSVP::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \knownPlugins\Event\RSVP();
                }

                if ($object->saveDataFromInput($this)) {
                    $this->forward($object->getURL());
                }

            }

        }

    }