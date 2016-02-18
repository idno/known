<?php

    namespace Idno\Caching {

        /**
         * Implement a persistent cache using XCache.
         */
        class XCache extends PersistentCache
        {

            public function delete($key)
            {
                xcache_dec('__known_xcache_size', strlen($this->load($key)));

                return xcache_unset($key);
            }

            public function load($key)
            {
                return xcache_get($key);
            }

            public function size()
            {
                return (int)$this->load('__known_xcache_size');
            }

            public function store($key, $value)
            {

                if (xcache_set($key, $value)) {
                    xcache_inc('__known_xcache_size', strlen($value));
                }

                return false;
            }

        }

    }