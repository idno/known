<?php

/**
 * Interface defining a mechanism to collect statistics about your known site.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Stats {

    abstract class StatisticsCollector extends \Idno\Common\Component {

        /**
         * Set timing values
         *
         * @param string $stat The metric(s) to set.
         * @param float $time The elapsed time (ms) to log
         * */
        abstract public function timing($stat, $time);

        /**
         * Set gauge to a value
         *
         * @param string $stat The metric(s) to set.
         * @param float $value The value for the stats.
         * */
        abstract public function gauge($stat, $value);

        /**
         * A "Set" is a count of unique events.
         * 
         * @param string $stat The metric to set.
         * @param float $value The value for the stats.
         * */
        abstract public function set($stat, $value);

        /**
         * Increments stats counters
         *
         * @param string $stat The metric to increment.
         * @return boolean
         * */
        abstract public function increment($stat);

        /**
         * Decrements stats counters.
         *
         * @param string $stat The metric to decrement.
         * @return boolean
         * */
        abstract public function decrement($stat);
    }

}
