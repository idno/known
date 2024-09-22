<?php

namespace Idno\Pages\Service\Web {

    class RemovePreview extends \Idno\Common\Page
    {

        function postContent()
        {

            $this->gatekeeper();

            \Idno\Core\Idno::site()->template()->setTemplateType('json');

            if (!empty($this->arguments[0])) {
                $object = \Idno\Common\Entity::getByID($this->arguments[0]);
                if (empty($object)) {
                    $object = \Idno\Common\Entity::getBySlug($this->arguments[0]);
                }
            }
            if (empty($object)) {
                $this->goneContent();
            }

            if (!$object->canEdit()) {
                $this->deniedContent();
            }

            $object->hide_preview = true;

            \Idno\Core\Idno::site()->response()->setContent(json_encode($object->save()));

        }

    }

}
