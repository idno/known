<?php

    namespace Idno\Caching {

        /**
         * Store values in memory for the lifetime of script execution.
         */
        class MemoryCache
            extends EphemeralCache
        {
            /// The cache
            private $cache = [];

            public function delete($key)
            {
                unset($this->cache[$key]);

                return true;
            }

            public function load($key)
            {

                if (isset($this->cache[$key])) {
                    if (\Idno\Core\Idno::site()->config()->debug) {
                        Idno\Core\Idno::site()->logging->debug("Loading $key");
                    }

                    return $this->cache[$key];
                }

                if (\Idno\Core\Idno::site()->config()->debug) {
                    Idno\Core\Idno::site()->logging->debug("$key not cached");
                }

                return false;
            }

            public function size()
            {
                return count($this->cache);
            }

            public function store($key, $value)
            {
                if (\Idno\Core\Idno::site()->config()->debug)
                    Idno\Core\Idno::site()->logging->debug("Caching $key");

                $this->cache[$key] = $value;

                return true;
            }

        }
    }