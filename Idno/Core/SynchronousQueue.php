<?php

namespace Idno\Core;

class SynchronousQueue extends EventQueue
{

    private $results = array();

    function enqueue($queueName, $eventName, array $eventData)
    {
        $id     = md5(microtime(true) . mt_rand() . $eventName);
        try {
            $result = Idno::site()->events()->triggerEvent($eventName, $eventData);
            $this->results[$id] = $result;
        } catch (\Exception $e) {
            \Idno\Core\Idno::site()->logging()->error($e->getMessage());
            $this->results[$id] = false;
        }
        return $id;
    }

    function isComplete($id)
    {
        return isset($this->results[$id]);
    }

    function getResult($id)
    {
        return $this->results[$id];
    }

}
