<?php

namespace Idno\Pages\Service\Queues {

    use Idno\Core\Idno;

    class GC extends \Idno\Common\Page {
        
        public function getContent() {
            
            Idno::site()->template()->setTemplateType('json');
            
            \Idno\Core\Service::gatekeeper();
            
            $eventqueue = \Idno\Core\Idno::site()->queue();
            if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue) 
                throw new \RuntimeException("Service can't run unless Known's queue is Asynchronous!");
            
            $queue = $this->getInput('queue', 'default');
            
            \Idno\Core\Idno::site()->logging()->debug("Garbage Collecting event queue $queue");
                        
            $eventqueue->gc(300, $queue);
            
            Idno::site()->template()->__([
                'gc' => true
            ])->drawPage();
        }
    }
}