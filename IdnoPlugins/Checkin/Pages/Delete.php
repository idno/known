<?php

    namespace IdnoPlugins\Checkin\Pages {

        class Delete extends \Idno\Common\Page {

            function postContent() {
                $this->createGatekeeper();

                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Checkin\Checkin::getByID($this->arguments[0]);
                }
                if (empty($object)) $this->forward();
                if (!$object->canEdit()) {
                    $this->setResponse(403);
                    \Idno\Core\Idno::site()->session()->addErrorMessage("You don't have permission to perform this task.");
                    $this->forward();
                }

                if ($object->delete()) {
                    \Idno\Core\Idno::site()->session()->addMessage('Your checkin was deleted.');
                } else {
                    \Idno\Core\Idno::site()->session()->addErrorMessage("We couldn't delete " . $object->getTitle() . ".");
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

        }

    }