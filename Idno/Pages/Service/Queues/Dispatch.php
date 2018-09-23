<?php

namespace Idno\Pages\Service\Queues {

    use Idno\Core\Idno;

    class Dispatch extends \Idno\Common\Page
    {

        public function getContent()
        {

            Idno::site()->template()->setTemplateType('json');

            \Idno\Core\Service::gatekeeper();

            if (!empty($this->arguments[0])) {
                $object = \Idno\Common\Entity::getByID($this->arguments[0]);
            }
            if (empty($object)) $this->noContent();

            $eventqueue = \Idno\Core\Idno::site()->queue();
            if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue)
                throw new \RuntimeException("Service can't run unless Known's queue is Asynchronous!");

            $result = $eventqueue->dispatch($object);

            Idno::site()->template()->__([
                'event_id' => $this->arguments[0],
                'complete' => $object->complete,
                'completed_ts' => $object->completedTs
            ])->drawPage();

        }
    }
}