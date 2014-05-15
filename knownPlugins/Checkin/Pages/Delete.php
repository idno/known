<?php

    namespace knownPlugins\Checkin\Pages {

        class Delete extends \known\Common\Page {

            function postContent() {
                $this->gatekeeper();

                if (!empty($this->arguments)) {
                    $object = \knownPlugins\Checkin\Checkin::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward();
                if (!$object->canEdit()) {
                    $this->setResponse(403);
                    \known\Core\site()->session()->addMessage("You don't have permission to perform this task.");
                    $this->forward();
                }

                if ($object->delete()) {
                    \known\Core\site()->session()->addMessage('Your checkin was deleted.');
                } else {
                    \known\Core\site()->session()->addMessage("We couldn't delete " . $object->getTitle() . ".");
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }