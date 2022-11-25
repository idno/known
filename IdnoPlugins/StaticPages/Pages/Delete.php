<?php

namespace IdnoPlugins\StaticPages\Pages {

    class Delete extends \Idno\Common\Page
    {

        function getContent()
        {

            $this->createGatekeeper();    // This functionality is for logged-in users only

            // Are we loading an entity?
            if (!empty($this->arguments)) {
                $object = \IdnoPlugins\StaticPages\StaticPage::getByID($this->arguments[0]);
            } else {
                // TODO 404
                $this->forward();
            }

            $t    = \Idno\Core\Idno::site()->template();
            $body = $t->__(array(
                'object' => $object
            ))->draw('entity/StaticPages/delete');

            if (!empty($this->xhr)) {
                echo $body;
            } else {
                $t->__(array('body' => $body, 'title' => \Idno\Core\Idno::site()->language()->_("Delete %s", [$object->getTitle()])))->drawPage();
            }
        }

        function postContent()
        {
            $this->createGatekeeper();

            if (!empty($this->arguments)) {
                $object = \IdnoPlugins\StaticPages\StaticPage::getByID($this->arguments[0]);
            }
            if (empty($object)) $this->forward();
            if (!$object->canEdit()) {
                $this->setResponse(403);
                \Idno\Core\Idno::site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_("You don't have permission to perform this task."));
                $this->forward();
            }

            if ($object->delete()) {
                \Idno\Core\Idno::site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_('Your page was deleted.'));
            } else {
                \Idno\Core\Idno::site()->session()->addMessage(\Idno\Core\Idno::site()->language()->esc_("We couldn't delete %s.", [$object->getTitle()]));
            }
            $this->forward($_SERVER['HTTP_REFERER']);
        }

    }

}

