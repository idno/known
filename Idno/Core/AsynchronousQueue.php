<?php

namespace Idno\Core;

/**
 * Execute events asynchronously, using database as queue store.
 * In the future this may be replaced by a more flexible messaging / queue service
 */
class AsynchronousQueue extends EventQueue
{
    function registerPages() {
        \Idno\Core\Idno::site()->addPageHandler('/service/queue/list/?', '\Idno\Pages\Service\Queues\Queue');
        \Idno\Core\Idno::site()->addPageHandler('/service/queue/gc/?', '\Idno\Pages\Service\Queues\GC');
    }
    
    function enqueue($queueName, $eventName, array $eventData)
    {
        if (empty($queueName))
            $queueName = 'default';
        
        $queuedEvent = new \Idno\Entities\AsynchronousQueuedEvent();
        $queuedEvent->queue = $queueName;
        $queuedEvent->event = $eventName;
        $queuedEvent->eventData = serialize($eventData);
        $queuedEvent->runAsContext = \Idno\Core\Idno::site()->session()->currentUserUUID();
        $queuedEvent->complete = false;
        $queuedEvent->queuedTs = time();
        
        \Idno\Core\Idno::site()->logging()->debug("Enqueued asynchronous event $eventName on queue $queueName");
        
        return $queuedEvent->save();
    }

    function isComplete($id)
    {
        $event = \Idno\Entities\AsynchronousQueuedEvent::getByID($id);
        if (!empty($event)) {
            return $event->complete;
        }
        
        return false;
    }

    function getResult($id)
    {
        $event = \Idno\Entities\AsynchronousQueuedEvent::getByID($id);
        if (!empty($event)) {
            return unserialize($event->result);
        }
    }
    
    /**
     * Dispatch event. 
     * @param \Idno\Entities\AsynchronousQueuedEvent $event
     */
    function dispatch(\Idno\Entities\AsynchronousQueuedEvent &$event) {
        
        if (empty($event))
            throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('No event passed'));
        
        if (!($event instanceof \Idno\Entities\AsynchronousQueuedEvent))
            throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Event passed is not a queued event, and so can\'t be dispatched'));
        
        if ($event->complete)
            throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Sorry, this event has already been dispatched (but not yet cleaned up)'));
                
        try {
        
            $username = "ANONYMOUS";
            
            if (!empty($event->runAsContext)) {
                $user = \Idno\Entities\User::getByUUID($event->runAsContext);
                if (empty($user))
                    throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_("Invalid user (%s) given for runAsContext, aborting", [$event->runAsContext]));

                \Idno\Core\Idno::site()->session()->logUserOn($user);
                
                $username = $user->getName();
            }

            \Idno\Core\Idno::site()->logging()->info("[".date('r')."] Dispatching event " . $event->getID() . ": {$event->event} as $username queued at " . date('r', $event->queuedTs));
            
            $result = \Idno\Core\Idno::site()->triggerEvent($event->event, unserialize($event->eventData));
            
            $event->result = serialize($result);
            
            \Idno\Core\Idno::site()->session()->logUserOff();
            
        } catch (\Exception $e) {
            \Idno\Core\Idno::site()->logging()->error($e->getMessage());
            $event->error = $e->getMessage();
        }
        
        $event->complete = true;
        $event->completedTs = time();
        
        return $event->save();
    }
    
    /** 
     * Garbage collect old completed event
     */
    function gc($timeago = 300, $queue = null) {
        
        \Idno\Core\Idno::site()->logging()->debug("[".date('r')."] Garbage collecting...");
        
        $search = [
            'completedTs' => [
                '&lt' => time() - $timeago
            ],
            'complete' => true,
        ];
        
        if (!empty($queue))
            $search['queue'] = $queue;
        
        if ($events = \Idno\Entities\AsynchronousQueuedEvent::get($search)) {
            
            foreach($events as $event) {
                
                \Idno\Core\Idno::site()->logging()->debug("AsynchronousQueue::gc($timeago) removing " . $event->getID() . " - {$event->event} in queue {$event->queue}, completed " . date('r', $event->completedTs));
                $event->delete();
            }
        }
    }

}
