<?php

    namespace knownPlugins\Text\Pages {

        class Delete extends \known\Common\Page {

            function getContent() {

                $this->gatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Text\Entry::getByID($this->arguments[0]);
                } else {
                    // TODO 404
                    $this->forward();
                }

                $t = \known\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Entry/delete');

                if (!empty($this->xhr)) {
                    echo $body;
                } else {
                    $t->__(array('body' => $body, 'title' => "Delete " . $object->getTitle()))->drawPage();
                }
            }

            function postContent() {
                $this->gatekeeper();

                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Text\Entry::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward();
                if (!$object->canEdit()) {
                    $this->setResponse(403);
                    \known\Core\site()->session()->addMessage("You don't have permission to perform this task.");
                    $this->forward();
                }

                if ($object->delete()) {
                    \known\Core\site()->session()->addMessage($object->getTitle() . ' was deleted.');
                } else {
                    \known\Core\site()->session()->addMessage("We couldn't delete " . $object->getTitle() . ".");
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }