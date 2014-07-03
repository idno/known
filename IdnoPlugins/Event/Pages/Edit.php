<?php

    namespace IdnoPlugins\Event\Pages {

        use Idno\Pages\Entity\Autosave;

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Event\Event::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Event\Event();
                }

                $t = \Idno\Core\site()->template();
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
                $this->createGatekeeper();

                $new = false;
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Event\Event::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Event\Event();
                }

                if ($object->saveDataFromInput($this)) {
                    (new \Idno\Core\Autosave())->clearContext('event');
                    //$this->forward(\Idno\Core\site()->config()->getURL() . 'content/all/#feed');
                    $this->forward($object->getURL());
                }

            }

        }

    }