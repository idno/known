<?php

    /**
     * Withdraw syndication
     */

namespace Idno\Pages\Entity {

    class Withdraw extends \Idno\Common\Page
    {

        // Handle GET requests to the withdrawal endpoint

        function getContent()
        {
            if (!empty($this->arguments[0])) {
                $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                $this->forward($object->getDisplayURL());
            }
            if (empty($object)) {
                $this->setResponse(404);
                $content = \Idno\Core\Idno::site()->template()->__(array('body' => \Idno\Core\Idno::site()->template()->draw('404'), 'title' => 'Not found'))->drawPage(false);
                \Idno\Core\Idno::site()->response()->setContent($content);
                \Idno\Core\Idno::site()->sendResponse();
            }
        }

        // Handle POST requests to the withdrawal endpoint

        function postContent()
        {
            if (!empty($this->arguments[0])) {
                $object = \Idno\Common\Entity::getByID($this->arguments[0]);
            }
            if (empty($object)) {
                $this->setResponse(404);
                $content = \Idno\Core\Idno::site()->template()->__(array('body' => \Idno\Core\Idno::site()->template()->draw('404'), 'title' => 'Not found'))->drawPage(false);
                \Idno\Core\Idno::site()->response()->setContent($content);
                \Idno\Core\Idno::site()->sendResponse();
            }

            if (!$object->canEdit()) {
                $this->setResponse(403);
                $content = \Idno\Core\Idno::site()->template()->__(array('body' => \Idno\Core\Idno::site()->template()->draw('403'), 'title' => 'Permission denied'))->drawPage(false);
                \Idno\Core\Idno::site()->response()->setContent($content);
                \Idno\Core\Idno::site()->sendResponse();
            }

            $object->unsyndicate();

            \Idno\Core\Idno::site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_("We removed copies on all the syndicated sites."));

            $this->forward($object->getDisplayURL());

        }

    }

}

