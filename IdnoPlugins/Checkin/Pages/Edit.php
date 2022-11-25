<?php

namespace IdnoPlugins\Checkin\Pages {

    class Edit extends \Idno\Common\Page
    {

        function getContent()
        {

            $this->createGatekeeper();    // This functionality is for logged-in users only

            // Are we loading an entity?
            if (!empty($this->arguments)) {
                $object = \IdnoPlugins\Checkin\Checkin::getByID($this->arguments[0]);
            } else {
                $object = new \IdnoPlugins\Checkin\Checkin();
            }

            if (!$object) $this->noContent();

            if ($owner = $object->getOwner()) {
                $this->setOwner($owner);
            }

            $t = \Idno\Core\Idno::site()->template();
            $body = $t->__(array(
                'object' => $object
            ))->draw('entity/Checkin/edit');

            if (empty($object)) {
                $title = \Idno\Core\Idno::site()->language()->_('Where are you?');
            } else {
                $title = \Idno\Core\Idno::site()->language()->_('Edit checkin');
            }

            if (!empty($this->xhr)) {
                echo $body;
            } else {
                $t->__(array('body' => $body, 'title' => $title))->drawPage();
            }
        }

        function postContent()
        {
            $this->createGatekeeper();

            $new = false;
            if (!empty($this->arguments)) {
                $object = \IdnoPlugins\Checkin\Checkin::getByID($this->arguments[0]);
            }
            if (empty($object)) {
                $object = new \IdnoPlugins\Checkin\Checkin();
            }

            if ($object->saveDataFromInput()) {
                $forward = $this->getInput('forward-to', $object->getDisplayURL());
                $this->forward($forward);
            }

        }

    }

}
