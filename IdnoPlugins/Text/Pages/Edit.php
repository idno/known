<?php

    namespace IdnoPlugins\Text\Pages {

        use Idno\Core\Autosave;

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Text\Entry::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Text\Entry();
                    $autosave = new \Idno\Core\Autosave();
                    foreach (array(
                        'title', 'body'
                    ) as $field) {
                        $object->$field = $autosave->getValue('entry', $field);
                    }
                }

                if ($owner = $object->getOwner()) {
                    $this->setOwner($owner);
                }

                $t = \Idno\Core\Idno::site()->template();
                $edit_body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Entry/edit');

                $body = $t->__(['body' => $edit_body])->draw('entity/editwrapper');

                if (empty($object->_id)) {
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
                $this->createGatekeeper();

                $new = false;
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Text\Entry::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Text\Entry();
                }

                if ($object->saveDataFromInput()) {
                    (new \Idno\Core\Autosave())->clearContext('entry');
                    //$this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'content/all/');
                    //$this->forward($object->getDisplayURL());
                    $forward = $this->getInput('forward-to', $object->getDisplayURL());
                    $this->forward($forward);
                }

            }

        }

    }
