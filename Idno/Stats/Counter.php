<?php

/**
 * Simple stats counter
 *
 * @package    idno
 * @subpackage core
 */

namespace Idno\Stats {

    class Counter
    {

        /**
         * Array of counters
         */
        private static $counters = [];

        /**
         * Set the value of a counter.
         *
         * @param string $counter
         * @param int    $value
         */
        public static function set($counter, $value)
        {

            self::$counters[$counter] = $value;

        }

        /**
         * Increment a counter value.
         *
         * @param int $counter
         */
        public static function increment($counter)
        {

            $value = self::get($counter);
            $value ++;

            return self::set($counter, $value);
        }

        /**
         * Decrement a counter value.
         *
         * @param int $counter
         */
        public static function decrement($counter)
        {

            $value = self::get($counter);
            $value --;

            return self::set($counter, $value);
        }

        /**
         * Retrieve the counter value
         *
         * @param  int $counter
         * @return int
         */
        public static function get($counter)
        {

            if (isset(self::$counters[$counter])) {
                return self::$counters[$counter];
            }

            return 0;
        }

        /**
         * Retrieve all counters, sorted by counter name.
         */
        public static function getAll()
        {

            ksort(self::$counters);

            return self::$counters;
        }
    }

}
