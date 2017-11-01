<?php

namespace Idno\Entities {

    /**
     * Represents a queued event stored in the AsynchronousQueue.
     */
    class AsynchronousQueuedEvent extends \Idno\Entities\Object {
         
        public function save($add_to_feed = false, $feed_verb = 'post')
        {
            if (empty($this->queue)) {
                $this->queue = 'default';
            }

            return parent::save($add_to_feed, $feed_verb);
        }
            
        public static function getPendingFromQueue($queue = 'default', $limit = 10, $offset = 0) {
            return self::get(['queue' => $queue, 'complete' => ['$not' => ['$in' => [true]]]], [], $limit, $offset);
        }
        
        public static function getFromQueue($queue = 'default', $limit = 10, $offset = 0) {
            return self::get(['queue' => $queue], [], $limit, $offset);
        }
    }

}