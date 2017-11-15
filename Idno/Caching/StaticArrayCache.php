<?php

namespace Idno\Caching {

    /**
     * A version of ArrayCache which uses a shared array, this means it's safe to use "$foo = new StaticArrayCache()" and still have access
     * to values set elsewhere in the system.
     */
    class StaticArrayCache extends ArrayCache {

        private static $staticCache = [];

        public function delete($key) {
            unset(self::$staticCache[$key]);

            return true;
        }

        public function load($key) {

            if (isset(self::$staticCache[$key])) {
                return self::$staticCache[$key];
            }

            return false;
        }

        public function size() {
            return count(self::$staticCache);
        }

        public function store($key, $value) {

            self::$staticCache[$key] = $value;

            return true;
        }

    }

}
