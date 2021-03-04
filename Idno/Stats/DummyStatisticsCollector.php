<?php

/**
 * Null implementation of StatisticsCollector, so that the system always has one available.
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Stats {

    class DummyStatisticsCollector extends StatisticsCollector
    {

        public function decrement($stat)
        {
            return true;
        }

        public function gauge($stat, $value)
        {
            return true;
        }

        public function increment($stat)
        {
            return true;
        }

        public function set($stat, $value)
        {
            return true;
        }

        public function timing($stat, $time)
        {
            return true;
        }

    }
}
