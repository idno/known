<?php

/**
 * Simple timer interface
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Stats {
    
    class Timer {
        
        private static $timers = [];
        
        /**
         * Start a timer.
         * @param type $timer
         */
        public static function start($timer) {
            self::$timers[$timer] = microtime(true);
        }
        
        /**
         * Retrieve the current number of seconds (with milliseconds) since $timer was started.
         * @param type $timer
         */
        public static function value($timer) {
            
            $now = microtime(true);
            
            if (isset(self::$timers[$timer])) {
                return $now - self::$timers[$timer];
            }
            
            throw new \RuntimeException("Timer $timer has not been started.");
        }
        
    }
}