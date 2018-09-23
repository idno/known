<?php

namespace Idno\Pages\Service\Queues {

    use Idno\Core\Idno;

    class Queue extends \Idno\Common\Page {
        
        public function getContent() {
            
            Idno::site()->template()->setTemplateType('json');
            
            \Idno\Core\Service::gatekeeper();
            
            $limit = $this->getInput('limit', 10);
            $offset = $this->getInput('offset', 0);
            $queue = $this->getInput('queue', 'default');
            
            \Idno\Core\Idno::site()->logging()->debug("Displaying event queue from $queue");
            
            $array = [];
            if ($queue_list = \Idno\Entities\AsynchronousQueuedEvent::getPendingFromQueue($queue, $limit, $offset)) {
                foreach ($queue_list as $event) {
                    $array[] = (string)$event->getID();
                }
            }
            
            Idno::site()->template()->__([
                'queue' => $array,
            ])->drawPage();
        }
    }
}