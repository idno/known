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
                    $autosave = new \Idno\Core\Autosave();
                    foreach (array(
                        'title', 'summary', 'location', 'starttime', 'endtime', 'body'
                    ) as $field) {
                        $object->$field = $autosave->getValue('event', $field);
                    }
                }

                if ($owner = $object->getOwner()) {
                    $this->setOwner($owner);
                }

                $t = \Idno\Core\Idno::site()->template();
                $edit_body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Event/edit');

                $body = $t->__(['body' => $edit_body])->draw('entity/editwrapper');

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

                if ($object->saveDataFromInput()) {
                    (new \Idno\Core\Autosave())->clearContext('event');
                    $forward = $this->getInput('forward-to', $object->getDisplayURL());
                    $this->forward($forward);
                }

            }

        }

    }